<?php

namespace App\Model;

use Nette;
use Nette\Database\Context;

class Stats
{
    /** @var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

	public function getDataCountries($countries, $radTech, $moreTechs=False)
	{
		$radTechs = ($moreTechs) ? $radTech : array($radTech);
		$iterator = 0;
		$records = array();
		// Getting list of all existing radio technology types from AggregatedData table
		$aData = $this->database->table('AggregatedDataCountries');
		$radioTechList = $aData->select('DISTINCT radioTechnology')
				->order('radioTechnology ASC')
				->fetchPairs('radioTechnology', 'radioTechnology');

		// Getting list of all existing iso country codes from AggregatedData table
		$aData = $this->database->table('AggregatedDataCountries');
		$countryList = $aData->select('DISTINCT isoCountryCode')
				->order('isoCountryCode ASC')
				->fetchPairs('isoCountryCode', 'isoCountryCode');

		foreach ($countries as $country) {
			// Check if input country exist in AggregationData table
			if (!$this->in_array_insensitive($country, $countryList)) {
				continue;
			}
			foreach($radTechs as $radTech) {
				if (!$this->in_array_insensitive($radTech, $radioTechList)) {
                			continue;
				}
				// loading data from database for specific country and radio technology
				$aData = $this->database->table('AggregatedDataCountries');
				$selection = $aData->select('avgDownloadSpeed, avgLatency, avgQoe, medDownloadSpeed, medLatency, medQoe')
						->where('isoCountryCode = ? AND radioTechnology = ?', $country, $radTech);
				// parsing from single line selection, loop runs only once but did not get how to read from this special variable
				foreach ($selection as $one) {
					$avgSpeed = $one->avgDownloadSpeed;
					$avgLatency = $one->avgLatency;
					$avgQoe = $one->avgQoe;
					$medSpeed = $one->medDownloadSpeed;
					$medLatency = $one->medLatency;
					$medQoe = $one->medQoe;
				}

				// saving loaded data from database as record for better JSON formating
				$record = new \stdClass();
				$record->country = $country;
				$record->radTech = $radTech;
				$record->avgDownloadSpeed = $avgSpeed;
				$record->avgLatency = $avgLatency;
				$record->avgQoe = $avgQoe;
				$record->medDownloadSpeed = $medSpeed;
				$record->medLatency = $medLatency;
				$record->medQoe = $medQoe;
				
				// saving array of records with data to be encoded into JSON format
				$records[$iterator] = $record;;	
				$iterator = $iterator + 1;
			}
		}
		return $records;
	}
        
	public function getDataOperators($countries, $radTechs)
        {
                $iterator = 0;
                $records = array();
                // Getting list of all existing radio technology types from AggregatedData table
                $aData = $this->database->table('AggregatedDataOperators');
                $radioTechList = $aData->select('DISTINCT radioTechnology')
                                ->order('radioTechnology ASC')
                                ->fetchPairs('radioTechnology', 'radioTechnology');

                // Getting list of all existing iso country codes from AggregatedData table
                $aData = $this->database->table('AggregatedDataOperators');
                $countryList = $aData->select('DISTINCT isoCountryCode')
                                ->order('isoCountryCode ASC')
                                ->fetchPairs('isoCountryCode', 'isoCountryCode');

                // main iteration to get data from aggreagationdata table in database
                foreach ($radTechs as $radTech) {
                        // SQL ignore case but I am comparing arrays in PHP so need to check if this technology exist and ignoring case
                        if (!$this->in_array_insensitive($radTech, $radioTechList)) {
                                continue;
                        } else {
                                foreach ($countries as $country) {
                                        // Check if input country exist in AggregationData table
                                        if (!$this->in_array_insensitive($country, $countryList)) {
                                                continue;
                                        } else {
                                                $aData = $this->database->table('AggregatedDataOperators');
                                                $operatorList = $aData->select('DISTINCT operator')
                                                                        ->where('isoCountryCode = ?', $country)
                                                                        ->order('operator ASC')
                                                                        ->fetchPairs('operator', 'operator');
                                                foreach ($operatorList as $operator) {

                                                        // loading data from database for specific country and radio technology
                                                        $aData = $this->database->table('AggregatedDataOperators');
                                                        $selection = $aData->select('avgDownloadSpeed, avgLatency, avgQoe, medDownloadSpeed, medLatency, medQoe')
                                                                        ->where('isoCountryCode = ? AND radioTechnology = ? AND operator = ?', $country, $radTech, $operator);
                                                        // parsing from single line selection, loop runs only once but did not get how to read from this special variable
                                                        foreach ($selection as $one) {
                                                                $avgSpeed = $one->avgDownloadSpeed;
                                                                $avgLatency = $one->avgLatency;
                                                                $avgQoe = $one->avgQoe;
                                                                $medSpeed = $one->medDownloadSpeed;
                                                                $medLatency = $one->medLatency;
                                                                $medQoe = $one->medQoe;
                                                        }

                                                        // saving loaded data from database as record for better JSON formating
                                                        $record = new \stdClass();
                                                        $record->country = $country;
                                                        $record->radTech = $radTech;
                                                        $record->operator = $operator;
                                                        $record->avgDownloadSpeed = $avgSpeed;
                                                        $record->avgLatency = $avgLatency;
                                                        $record->avgQoe = $avgQoe;
                                                        $record->medDownloadSpeed = $medSpeed;
                                                        $record->medLatency = $medLatency;
                                                        $record->medQoe = $medQoe;

                                                        // saving array of records with data to be encoded into JSON format
                                                        $records[$iterator] = $record;;
                                                        $iterator = $iterator + 1;
                                                }
                                        }
                                }
                        }
                }

                return $records;
	}



	// function in_array that ignore case sensitivity, transfer all data in array and search stuff to lowercase
	private function in_array_insensitive($search, $list) {
   		$search = strtolower($search);
   		foreach($list as $a => $b) {
      			$list[$a] = strtolower($b);
   		}
   		return in_array($search, $list);
	}
}
