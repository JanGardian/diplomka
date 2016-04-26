<?php

namespace App\Presenters;

use Nette;
use Nette\Database\Context;
use Nette\Application\UI;


class HomepagePresenter extends Nette\Application\UI\Presenter
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

	public function renderDefault()
	{
		#print_r($this->template->_POST['EDGE']);
		#https://github.com/nnnick/Chart.js
		#$t = $_POST['GPRS'];
		#echo "$t";
	}

	#public function renderAggregations()
	#{
	#	var_dump('it works!');exit;
	#}:q

	protected function createComponentFilterForm()
	{
		// Getting list of all existing iso country codes from AggregatedData table
		$aData = $this->database->table('AggregatedData');
		$countries = $aData->select('DISTINCT isoCountryCode')
			->order('isoCountryCode ASC')
			->fetchPairs('isoCountryCode', 'isoCountryCode');

		// Getting list of all existing radio technology types from AggregatedData table
		$aData = $this->database->table('AggregatedData');
		$radioTechList = $aData->select('DISTINCT radioTechnology')
			->order('radioTechnology ASC')
			->fetchPairs('radioTechnology', 'radioTechnology');

		$form = new UI\Form();
		$form->addCheckboxList('tech', 'Technologie', $radioTechList);
		$form->addMultiSelect('country', 'Zem:', $countries)
			->setAttribute('size', 12);
		$form->addSubmit('submit', 'Vykreslit');
		$form->onSuccess[] = array($this, 'filterFormSuccess');

		// setup form rendering
		$renderer = $form->getRenderer();
		$renderer->wrappers['controls']['container'] = NULL;
		$renderer->wrappers['pair']['container'] = 'div class=form-group';
		$renderer->wrappers['pair']['.error'] = 'has-error';
		$renderer->wrappers['control']['container'] = 'div class=col-sm-9';
		$renderer->wrappers['label']['container'] = 'div class="col-sm-3 control-label"';
		$renderer->wrappers['control']['description'] = 'span class=help-block';
		$renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';
		// make form and controls compatible with Twitter Bootstrap
		$form->getElementPrototype()->class('form-horizontal');
		foreach ($form->getControls() as $control) {
			$type = $control->getOption('type');
			if ($type === 'button') {
				$control->getControlPrototype()->addClass(empty($usedPrimary) ? 'btn btn-primary' : 'btn btn-default');
				$usedPrimary = TRUE;
			} elseif (in_array($type, array('text', 'textarea', 'select'), TRUE)) {
				$control->getControlPrototype()->addClass('form-control');
			} elseif (in_array($type, array('checkbox', 'radio'), TRUE)) {
				$control->getSeparatorPrototype()->setName('div')->addClass($control->getControlPrototype()->type);
			}
		}

		return $form;
	}

	public function filterFormSuccess(UI\Form $form, $values)
	{
		//$records = $this->stats->getData(array('CZ', 'GB'), array('LTE', 'EDGE'));
		$data = array();
		$data[] = array('Technology', 'O2', 'Vodafone', 'T-Com');
		$data[] = array('LTE', 2, 4, 8);
		$data[] = array('EDGE', 12, 3, 5);
		$data[] = array('GPRG', 1, 22, 9);
		$this->template->data = $data;
	}
}
