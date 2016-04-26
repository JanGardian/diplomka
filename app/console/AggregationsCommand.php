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
	function removeFromArray($array, $value) {
                return array_values(array_diff($array, array($value)));
        }

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

        // Initial setup for $dataLimit value, how many last records in table MeasuredData will be used for aggregation
        $dataLimit = 1000;
        $avgSpeed = 0;
        $avgLatency = 0;
        $avgQoe = 0;
	$medSpeed = 0;
        $medLatency = 0;
        $medQoe = 0;

        // $measures is array on which we count average value in aggregation
        $measures = array('qoe','downloadSpeed','latency');

        // Getting list of all existing radio technology types from MeasuredData table
        $mData = $this->database->table('MeasuredData');
        $radioTechList = $mData->select('DISTINCT radioTechnology')
                        ->order('radioTechnology ASC')
                        ->fetchPairs('radioTechnology', 'radioTechnology');
        $radioTechList = removeFromArray($radioTechList, "");
	$radioTechList = removeFromArray($radioTechList, "unknown");
        #array_push($radioTechList, "Wi-Fi");

        // Getting list of all existing iso country codes from MeasuredData table
        $mData = $this->database->table('MeasuredData');
        $countryList = $mData->select('DISTINCT isoCountryCode')
                        ->order('isoCountryCode ASC')
                        ->fetchPairs('isoCountryCode', 'isoCountryCode');
        $countryList = removeFromArray($countryList, "");

	// Main loop is based on each country, another subloop based on radio technology type
        foreach ($countryList as $country) {
                $mData = $this->database->table('MeasuredData');
                $operatorList = $mData->select('DISTINCT operator')
                        ->where('isoCountryCode = ?', $country)
                        ->order('operator ASC')
                        ->fetchPairs('operator', 'operator');
                $operatorList = removeFromArray($operatorList, "(null)");
                foreach ($operatorList as $operator) {
                        foreach ($radioTechList as $radTech) {

                                // Loading database selection to nette table format, selection for each country and technology
                                if ($radTech <> "Wi-Fi") { 
                                        $mData = $this->database->table('MeasuredData');
                                        $selection = $mData->select('isoCountryCode, radioTechnology, qoe, downloadSpeed, latency')
                                                           ->where('isoCountryCode = ? AND radioTechnology = ? AND operator = ? AND reachableVia <> ? AND Filtered = ?', $country, $radTech, $operator, "Wi-Fi", 0)
                                                           ->order('saved_date DESC')
                                                           ->limit($dataLimit);
                                }


                                // Counting average from loading data, loop is based per measure
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
                                        } else if ($selection->count("*") <= $dataLimit){
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
                                                $avg = round($sum / $dataLimit, 2);
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
				// INSERT new data as not exist yet in table
                                $aData = $this->database->table('AggregatedData');
                                $aData->where('isoCountryCode = ? AND radioTechnology = ? AND operator = ?', $country, $radTech, $operator);
                                if ($aData->count("*")==0) {
                                        $this->database->query('INSERT INTO AggregatedData', array(
                                                'isoCountryCode' => $country,
                                                'operator' => $operator,
                                                'radioTechnology' => $radTech,
                                                'avgDownloadSpeed' => (double) $avgSpeed,
                                                'avgLatency' => (double) $avgLatency,
                                                'avgQoe' => (double) $avgQoe,
						'medDownloadSpeed' => (double) $medSpeed,
						'medLatency' => (double) $medLatency,
						'medQoe' => (double) $medQoe,
                                        ));
                                // UPDATE data as they are already in table 
                                } else {
                                        $this->database->query('UPDATE AggregatedData SET ? WHERE isoCountryCode = ? AND radioTechnology = ? AND operator = ?', array(
                                                'avgDownloadSpeed' => (double) $avgSpeed,
                                                'avgLatency' => (double) $avgLatency,
                                                'avgQoe' => (double) $avgQoe,
						'medDownloadSpeed' => (double) $medSpeed,
                                                'medLatency' => (double) $medLatency,
                                                'medQoe' => (double) $medQoe,
                                                ), $country, $radTech, $operator);
                                }
                        }
                }
        }

	exit;
    }
}
