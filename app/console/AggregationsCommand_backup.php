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
	// Initial setup for $dataLimit value, how many last records in table MeasuredData will be used for aggregation
	$dataLimit = 1000;
	$newSpeed = 0;
        $newLatency = 0;
        $newQoe = 0;
	// $measures is array on which we count average value in aggregation
	$measures = array('qoe','downloadSpeed','latency');
	
	// Getting list of all existing radio technology types from MeasuredData table
	$mData = $this->database->table('MeasuredData');
        $radioTechList = $mData->select('DISTINCT radioTechnology')
                        ->order('radioTechnology ASC')
                        ->fetchPairs('radioTechnology', 'radioTechnology');
	
	// Getting list of all existing iso country codes from MeasuredData table
	$mData = $this->database->table('MeasuredData');
        $countryList = $mData->select('DISTINCT isoCountryCode')
                        ->order('isoCountryCode ASC')
                        ->fetchPairs('isoCountryCode', 'isoCountryCode');

	// Main loop is based on each country, another subloop based on radio technology type
        foreach ($countryList as $country) {
		if ($country=="") { continue; } else 
		{
                foreach ($radioTechList as $radTech) {
                       	if ($radTech=="") { continue; } else 
			{
			// Loading database selection to nette table format, selection for each country and technology
			$mData = $this->database->table('MeasuredData');
			$selection = $mData->select('isoCountryCode, radioTechnology, qoe, downloadSpeed, latency')
				->where('isoCountryCode = ? AND radioTechnology = ?', $country, $radTech)
                               	->order('saved_date DESC')
			       	->limit($dataLimit);
			
			// Counting average from loading data, loop is based per measure
			foreach ($measures as $measure) {
				// No measured data found for specific country and specific technology
                                if ($selection->count("*") == 0) {
                                        $avg = 0;
				/* Selection length count does ignore limit option and always provide 
				   count of where selection, working like this in SQL as well
				*/
				// Checking if count is smaller then setup limit
                                } else if ($selection->count("*") <= $dataLimit){
                                        $avg = round($selection->sum($measure) / $selection->count("*"), 2);
				// Calculating average value by reading data per line from selection
				} else {
					$sum = 0;
					foreach ($selection as $line) {
						$sum = $sum + doubleval($line->$measure);
					}
					$avg = round($sum / $dataLimit, 2);
				}
				// Saving average value based on measure type
				switch ($measure) {
                                        case "downloadSpeed":
                                                $newSpeed = $avg;
                                                break;
                                        case "latency":
                                                $newLatency = $avg;
                                                break;
                                        case "qoe":
                                                $newQoe = $avg;
                                                break;
                                }
                        }
			}
			// Saving calculated values to AggregatedData table in database
		        $aData = $this->database->table('AggregatedData');
		        $aData->where('isoCountryCode = ? AND radioTechnology = ?', $country, $radTech);
			// INSERT new data as not exist yet in table
		        if ($aData->count("*")==0) {
		                $this->database->query('INSERT INTO AggregatedData', array(
		                         'isoCountryCode' => $country,
		                         'radioTechnology' => $radTech,
		                         'avgDownloadSpeed' => (double) $newSpeed,
		                         'avgLatency' => (double) $newLatency,
		                         'avgQoe' => (double) $newQoe,
		                ));
			// UPDATE data as they are already in table 
		        } else {
		                $this->database->query('UPDATE AggregatedData SET ? WHERE isoCountryCode = ? AND radioTechnology = ?', array(
		                         'avgDownloadSpeed' => (double) $newSpeed,
		                         'avgLatency' => (double) $newLatency,
		                         'avgQoe' => (double) $newQoe,
		                         ), $country, $radTech);
		        }
		}
		}
	}
	exit;
    }
}
