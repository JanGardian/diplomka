<?php
namespace App\Console;

use Nette;
use Nette\Database\Context;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AggregationsCommand extends Command
{
	/** @var Nette\Database\Context @inject */
   	public $database;

    protected function configure()
    {
        $this->setName('app:aggregations')
            ->setDescription('Run the aggregations');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
	// function to remove value from array based on value and not key
	function removeFromArray($array, $value) {
                return array_values(array_diff($array, array($value)));
        }

	// function to calculate Median value from array of numbers
	function calculateMedian($array)
        {
                $Count = count($array);
                if(array_keys($array) !== range(0,($Count-1)))
                {
                        return null;
                }
                rsort($array);
                $middle = round(count($array) / 2);
                $median = $array[$middle-1];
                return $median;
        }
	
	// function to calculate median and average value for specific database selection
	function calculateMeasures($selection, $limit)
	{
		// initializing variables with zero value
	        $avgSpeed = 0;
	        $avgLatency = 0;
	        $avgQoe = 0;
	        $medSpeed = 0;
	        $medLatency = 0;
	        $medQoe = 0;
		
		// $measures is array on which we count average value in aggregation
	        $measures = array('qoe','downloadSpeed','latency');
	
		// Counting average and median from loaded data in selection, loop is based per measure
                foreach ($measures as $measure) {
                        $measured = array();
                        // No measured data found for specific country and specific technology
                        if ($selection->count("*") == 0) {
                                $avg = 0;
                                $med = 0;
                        /* Selection length count does ignore limit option and always provide 
                           count of where selection, working like this in SQL as well
                        */
                        // Checking if count is smaller then setup limit
                        } else if ($selection->count("*") <= $limit){
                                $avg = round($selection->sum($measure) / $selection->count("*"), 2);
                                foreach ($selection as $line) {
                                        array_push($measured, $line->$measure);
                                }
                                $med = round(calculateMedian($measured), 4);
                        // Calculating average value by reading data per line from selection
                        } else {
                                $sum = 0;
                                foreach ($selection as $line) {
                                        array_push($measured, $line->$measure);
                                        $sum = $sum + doubleval($line->$measure);
                                }
                                $med = round(calculateMedian($measured), 4);
                                $avg = round($sum / $limit, 2);
                        }
                        // Saving average value based on measure type
                        switch ($measure) {
                                case "downloadSpeed":
                                        $medSpeed = $med;
                                        $avgSpeed = $avg;
                                        break;
                                case "latency":
                                        $medLatency = $med;
                                        $avgLatency = $avg;
                                        break;
                                case "qoe":
                                        $medQoe = $med;
                                        $avgQoe = $avg;
                                        break;
                        }
                }
		return array($avgSpeed, $avgLatency, $avgQoe, $medSpeed, $medLatency, $medQoe);	
	}

        // Initial setup for $dataLimit value, how many last records in table MeasuredData will be used for aggregation
        $dataLimit = 1000;

        // $measures is array on which we count average value in aggregation
        $measures = array('qoe','downloadSpeed','latency');

        // Getting list of all existing radio technology types from MeasuredData table
        $mData = $this->database->table('MeasuredData');
        $radioTechList = $mData->select('DISTINCT radioTechnology')
                        ->order('radioTechnology ASC')
                        ->fetchPairs('radioTechnology', 'radioTechnology');

	// Removing empty and unknown technology type from list and adding new technology Wi-Fi 
        $radioTechList = removeFromArray($radioTechList, "");
	$radioTechList = removeFromArray($radioTechList, "unknown");
        array_push($radioTechList, "Wi-Fi");

        // Getting list of all existing iso country codes from MeasuredData table
        $mData = $this->database->table('MeasuredData');
        $countryList = $mData->select('DISTINCT isoCountryCode')
                        ->order('isoCountryCode ASC')
                        ->fetchPairs('isoCountryCode', 'isoCountryCode');
        $countryList = removeFromArray($countryList, "");

	


	// Main loop is based on each country, another subloop based on radio technology type
        foreach ($countryList as $country) {
                
		// Calculating averages and medians per Country and radio technology and saving to table AggregatedDataCountries
		foreach ($radioTechList as $radTech) {
			if ($radTech <> "Wi-Fi") {
				$selection = $mData->select('isoCountryCode, radioTechnology, qoe, downloadSpeed, latency')
                                                   ->where('isoCountryCode = ? AND radioTechnology = ? AND reachableVia <> ? AND Filtered = ?', $country, $radTech, "Wi-Fi", 0)
                                                   ->order('saved_date DESC')
                                                   ->limit($dataLimit);
		
				$mData = $this->database->table('MeasuredData');
			} else {
				$selection = $mData->select('isoCountryCode, radioTechnology, qoe, downloadSpeed, latency')
                                                   ->where('isoCountryCode = ? AND reachableVia = ? AND Filtered = ?', $country, "Wi-Fi", 0)
                                                   ->order('saved_date DESC')
                                                   ->limit($dataLimit);
			}
			
			$measured = calculateMeasures($selection, $dataLimit);		
			
			// INSERT new data as not exist yet in AggregatedDataCountries table
                        $aData = $this->database->table('AggregatedDataCountries');
                        $aData->where('isoCountryCode = ? AND radioTechnology = ? ', $country, $radTech);
                        if ($aData->count("*")==0) {
                                $this->database->query('INSERT INTO AggregatedDataCountries', array(
                                        'isoCountryCode' => $country,
                                        'radioTechnology' => $radTech,
                                        'avgDownloadSpeed' => (double) $measured[0],
                                        'avgLatency' => (double) $measured[1],
                                        'avgQoe' => (double) $measured[2],
                                        'medDownloadSpeed' => (double) $measured[3],
                                        'medLatency' => (double) $measured[4],
                                        'medQoe' => (double) $measured[5],
                                ));
                        // UPDATE data with new calculated values in AggregatedDataCountries table 
                        } else {
                                $this->database->query('UPDATE AggregatedDataCountries SET ? WHERE isoCountryCode = ? AND radioTechnology = ?', array(
                                        'avgDownloadSpeed' => (double) $measured[0],
                                        'avgLatency' => (double) $measured[1],
                                        'avgQoe' => (double) $measured[2],
                                        'medDownloadSpeed' => (double) $measured[3],
                                        'medLatency' => (double) $measured[4],
                                        'medQoe' => (double) $measured[5],
                                        ), $country, $radTech);
                        }

		
		}
		
		// Calculating averages and medians per each Operator in Country and radio technology and saving to table AggregatedDataOperators
		$mData = $this->database->table('MeasuredData');
                $operatorList = $mData->select('DISTINCT operator')
                        ->where('isoCountryCode = ?', $country)
                        ->order('operator ASC')
                        ->fetchPairs('operator', 'operator');
                $operatorList = removeFromArray($operatorList, "(null)");
                foreach ($operatorList as $operator) {
                        foreach ($radioTechList as $radTech) {

                                // Loading database selection to nette table format, selection for each country, technology and operator
				$mData = $this->database->table('MeasuredData');
                                if ($radTech <> "Wi-Fi") { 
                                        $selection = $mData->select('isoCountryCode, radioTechnology, qoe, downloadSpeed, latency')
                                                           ->where('isoCountryCode = ? AND radioTechnology = ? AND operator = ? AND reachableVia <> ? AND Filtered = ?', $country, $radTech, $operator, "Wi-Fi", 0)
                                                           ->order('saved_date DESC')
                                                           ->limit($dataLimit);
                                } else { continue; }
				// this part of code was calculating average and median value for technology Wi-Fi for each operator
				/* else {
					$selection = $mData->select('isoCountryCode, radioTechnology, qoe, downloadSpeed, latency')
                                                           ->where('isoCountryCode = ?  AND operator = ? AND reachableVia = ? AND Filtered = ?', $country, $operator, "Wi-Fi", 0)
                                                           ->order('saved_date DESC')
                                                           ->limit($dataLimit);
				} */ 
				
				$measured = calculateMeasures($selection, $dataLimit);

				// INSERT new data as not exist yet in AggregatedDataOperators table
                                $aData = $this->database->table('AggregatedDataOperators');
                                $aData->where('isoCountryCode = ? AND radioTechnology = ? AND operator = ?', $country, $radTech, $operator);
                                if ($aData->count("*")==0) {
                                        $this->database->query('INSERT INTO AggregatedDataOperators', array(
                                                'isoCountryCode' => $country,
                                                'operator' => $operator,
                                                'radioTechnology' => $radTech,
                                                'avgDownloadSpeed' => (double) $measured[0],
                                                'avgLatency' => (double) $measured[1],
                                                'avgQoe' => (double) $measured[2],
						'medDownloadSpeed' => (double) $measured[3],
						'medLatency' => (double) $measured[4],
						'medQoe' => (double) $measured[5],
                                        ));
                                // UPDATE data with new calculated value in AggregatedDataOperators table
                                } else {
                                        $this->database->query('UPDATE AggregatedDataOperators SET ? WHERE isoCountryCode = ? AND radioTechnology = ? AND operator = ?', array(
						'avgDownloadSpeed' => (double) $measured[0],
                                                'avgLatency' => (double) $measured[1],
                                                'avgQoe' => (double) $measured[2],
                                                'medDownloadSpeed' => (double) $measured[3],
                                                'medLatency' => (double) $measured[4],
                                                'medQoe' => (double) $measured[5],
                                                ), $country, $radTech, $operator);
                                }
                        }
                }
        }

	exit;
    }
}
