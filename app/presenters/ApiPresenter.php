<?php

namespace App\Presenters;

use Nette;
use Nette\Database\Context;

class ApiPresenter extends Nette\Application\UI\Presenter
{
    /** @var Nette\Database\Context */
    private $database;
    /** @var \App\Model\Stats */
    private $stats;
   
    public function __construct(Nette\Database\Context $database, \App\Model\Stats $stats)
    {
        $this->database = $database;
        $this->stats = $stats;
    }	


    public function renderTest()
    {
	$records = array();

	$iterator = 0;
        $records = array();
        // Getting list of all existing radio technology types from AggregatedData table
        $aData = $this->database->table('AggregatedData');
        $radioTechList = $aData->select('DISTINCT radioTechnology')
                        ->order('radioTechnology ASC')
                        ->fetchPairs('radioTechnology', 'radioTechnology');

        // Getting list of all existing iso country codes from AggregatedData table
        $aData = $this->database->table('AggregatedData');
        $countryList = $aData->select('DISTINCT isoCountryCode')
                        ->order('isoCountryCode ASC')
                        ->fetchPairs('isoCountryCode', 'isoCountryCode');

        // main iteration to get data from aggreagationdata table in database
        #foreach ($radTechs as $radTech) {
        #        // SQL ignore case but I am comparing arrays in PHP so need to check if this technology exist and ignoring case
        #        if (!$this->in_array_insensitive($radTech, $radioTechList)) {
        #                continue;
        #        } else {
        #                foreach ($countries as $country) {
        #                        // Check if input country exist in AggregationData table
        #                        if (!$this->in_array_insensitive($country, $countryList)) {
        #                                continue;
        #                        } else {
	#				
        #                                // loading data from database for specific country and radio technology
        #                                $aData = $this->database->table('AggregatedData');
        #                                $selection = $aData->select('avgDownloadSpeed, avgLatency, avgQoe')
        #                                                ->where('isoCountryCode = ? AND radioTechnology = ?', $country, $radTech);
        #                                // parsing from single line selection, loop runs only once but did not get how to read from this special variable
        #                                foreach ($selection as $one) {
        #                                        $avgSpeed = $one->avgDownloadSpeed;
        #                                        $avgLatency = $one->avgLatency;
        #                                        $avgQoe = $one->avgQoe;
        #                                }

        #                                // saving loaded data from database as record for better JSON formating
        #                                $record = new \stdClass();
        #                                $record->country = $country;
        #                                $record->radTech = $radTech;
        #                                $record->avgDownloadSpeed = $avgSpeed;
        #                                $record->avgLatency = $avgLatency;
        #                                $record->avgQoe = $avgQoe;

        #                                // saving array of records with data to be encoded into JSON format
        #                                $records[$iterator] = $record;;
        #                                $iterator = $iterator + 1;
        #                        }
        #                }
        #        }
        #}

	// NEW via nettte
	$radTechs = (array)$this->request->getParameter('tech');
	//$tech = $this->request->getPost('tech');
	if (!$radTechs) {
		$this->sendResponse(new Nette\Application\Responses\JsonResponse($records));
	}
	$countries = (array)$this->request->getParameter('country');
	//$tech = $this->request->getPost('country');
	if (!$countries) {
		$this->sendResponse(new Nette\Application\Responses\JsonResponse($records));
	}

	
	/* OLD ONE with PHP check
	// checking input from URL under variable country
	if (isset($_GET['country']) && !empty($_GET['country'])) {
		// if list of countries save this as array
		if (is_array($_GET['country'])) {
			$countries = $_GET['country'];
		// else save only one country e.g. url= "?country=CZ"
                } else {
			$countries[0] = $_GET['country'];
                }
	// Not sure how to respond to empty input for countrie, for now only echo
	} else {
		echo "country need to be setup";
		exit;
	}	

	// checking input from URL under variable tech
	if (isset($_GET['tech']) && !empty($_GET['tech'])) {
		// if list of technologies save this as array
		if (is_array($_GET['tech'])) {
			$radTechs = $_GET['tech'];
		// else save only one technology
		} else {
			$radTechs[0] = $_GET['tech'];
		}
	}
	*/

	// send countries and technologies to service getData which communicate with database
	$records = $this->stats->getData($countries, $radTechs);
	
	// output of API in form of JSON array of requested technologies for requested countries
	$this->sendResponse(new Nette\Application\Responses\JsonResponse($records));
    }

}
