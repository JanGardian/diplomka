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


    public function renderOperators()
    {
	$records = array();

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

	// IF you want to read request for API from URL itself uncomment bellow code for getting Technologies
	$radTechs = (array)$this->request->getParameter('tech');
	
	// IF you want to read request for API from POST please uncomment bellow code for getting Technologies
	//$tech = $this->request->getPost('tech');

	if (!$radTechs) {
		$this->sendResponse(new Nette\Application\Responses\JsonResponse($records));
	}

	// IF you want to read request for API from URL itself uncomment bellow code line for getting countries
	$countries = (array)$this->request->getParameter('country');
	
	// IF you want to read request for API from POST please uncomment bellow code line for getting countries
	//$tech = $this->request->getPost('country');

	if (!$countries) {
		$this->sendResponse(new Nette\Application\Responses\JsonResponse($records));
	}
	

	// send countries and technologies to service getDataOperators which communicate with database
	$records = $this->stats->getDataOperators($countries, $radTechs);
	
	// output of API in form of JSON array of requested technologies for requested countries
	$this->sendResponse(new Nette\Application\Responses\JsonResponse($records));
    }

    public function renderCountries()
    {
	$records = array();

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

	// IF you want to read request for API from URL itself uncomment bellow code for getting Technologies
	$radTechs = (array)$this->request->getParameter('tech');
	
	// IF you want to read request for API from POST please uncomment bellow code for getting Technologies
	//$tech = $this->request->getPost('tech');

	if (!$radTechs) {
		$this->sendResponse(new Nette\Application\Responses\JsonResponse($records));
	}

	// IF you want to read request for API from URL itself uncomment bellow code for getting countries
	$countries = (array)$this->request->getParameter('country');
	
	// IF you want to read request for API from POST please uncomment bellow code for getting Technologies
	//$tech = $this->request->getPost('country');

	if (!$countries) {
		$this->sendResponse(new Nette\Application\Responses\JsonResponse($records));
	}
	

	// send countries and technologies to service getDataCountries which communicate with database
	$records = $this->stats->getDataCountries($countries, $radTechs, true);
	
	// output of API in form of JSON array of requested technologies for requested countries
	$this->sendResponse(new Nette\Application\Responses\JsonResponse($records));
    }

}
