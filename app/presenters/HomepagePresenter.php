<?php

namespace App\Presenters;

use Nette;
use Nette\Database\Context;
use Nette\Application\UI;


class HomepagePresenter extends Nette\Application\UI\Presenter
{
	protected $maxCountries = 10;
    	/** @var Nette\Database\Context */
    	private $database;
    	/** @var \App\Model\Stats */
    	private $stats;
	private $countries = array(
		'AF' => 'Afghanistan',
		'AX' => 'Aland Islands',
		'AL' => 'Albania',
		'DZ' => 'Algeria',
		'AS' => 'American Samoa',
		'AD' => 'Andorra',
		'AO' => 'Angola',
		'AI' => 'Anguilla',
		'AQ' => 'Antarctica',
		'AG' => 'Antigua And Barbuda',
		'AR' => 'Argentina',
		'AM' => 'Armenia',
		'AW' => 'Aruba',
		'AU' => 'Australia',
		'AT' => 'Austria',
		'AZ' => 'Azerbaijan',
		'BS' => 'Bahamas',
		'BH' => 'Bahrain',
		'BD' => 'Bangladesh',
		'BB' => 'Barbados',
		'BY' => 'Belarus',
		'BE' => 'Belgium',
		'BZ' => 'Belize',
		'BJ' => 'Benin',
		'BM' => 'Bermuda',
		'BT' => 'Bhutan',
		'BO' => 'Bolivia',
		'BA' => 'Bosnia And Herzegovina',
		'BW' => 'Botswana',
		'BV' => 'Bouvet Island',
		'BR' => 'Brazil',
		'IO' => 'British Indian Ocean Territory',
		'BN' => 'Brunei Darussalam',
		'BG' => 'Bulgaria',
		'BF' => 'Burkina Faso',
		'BI' => 'Burundi',
		'KH' => 'Cambodia',
		'CM' => 'Cameroon',
		'CA' => 'Canada',
		'CV' => 'Cape Verde',
		'KY' => 'Cayman Islands',
		'CF' => 'Central African Republic',
		'TD' => 'Chad',
		'CL' => 'Chile',
		'CN' => 'China',
		'CX' => 'Christmas Island',
		'CC' => 'Cocos (Keeling) Islands',
		'CO' => 'Colombia',
		'KM' => 'Comoros',
		'CG' => 'Congo',
		'CD' => 'Congo, Democratic Republic',
		'CK' => 'Cook Islands',
		'CR' => 'Costa Rica',
		'CI' => 'Cote D\'Ivoire',
		'HR' => 'Croatia',
		'CU' => 'Cuba',
		'CY' => 'Cyprus',
		'CZ' => 'Czech Republic',
		'DK' => 'Denmark',
		'DJ' => 'Djibouti',
		'DM' => 'Dominica',
		'DO' => 'Dominican Republic',
		'EC' => 'Ecuador',
		'EG' => 'Egypt',
		'SV' => 'El Salvador',
		'GQ' => 'Equatorial Guinea',
		'ER' => 'Eritrea',
		'EE' => 'Estonia',
		'ET' => 'Ethiopia',
		'FK' => 'Falkland Islands (Malvinas)',
		'FO' => 'Faroe Islands',
		'FJ' => 'Fiji',
		'FI' => 'Finland',
		'FR' => 'France',
		'GF' => 'French Guiana',
		'PF' => 'French Polynesia',
		'TF' => 'French Southern Territories',
		'GA' => 'Gabon',
		'GM' => 'Gambia',
		'GE' => 'Georgia',
		'DE' => 'Germany',
		'GH' => 'Ghana',
		'GI' => 'Gibraltar',
		'GR' => 'Greece',
		'GL' => 'Greenland',
		'GD' => 'Grenada',
		'GP' => 'Guadeloupe',
		'GU' => 'Guam',
		'GT' => 'Guatemala',
		'GG' => 'Guernsey',
		'GN' => 'Guinea',
		'GW' => 'Guinea-Bissau',
		'GY' => 'Guyana',
		'HT' => 'Haiti',
		'HM' => 'Heard Island & Mcdonald Islands',
		'VA' => 'Holy See (Vatican City State)',
		'HN' => 'Honduras',
		'HK' => 'Hong Kong',
		'HU' => 'Hungary',
		'IS' => 'Iceland',
		'IN' => 'India',
		'ID' => 'Indonesia',
		'IR' => 'Iran, Islamic Republic Of',
		'IQ' => 'Iraq',
		'IE' => 'Ireland',
		'IM' => 'Isle Of Man',
		'IL' => 'Israel',
		'IT' => 'Italy',
		'JM' => 'Jamaica',
		'JP' => 'Japan',
		'JE' => 'Jersey',
		'JO' => 'Jordan',
		'KZ' => 'Kazakhstan',
		'KE' => 'Kenya',
		'KI' => 'Kiribati',
		'KR' => 'Korea',
		'KW' => 'Kuwait',
		'KG' => 'Kyrgyzstan',
		'LA' => 'Lao People\'s Democratic Republic',
		'LV' => 'Latvia',
		'LB' => 'Lebanon',
		'LS' => 'Lesotho',
		'LR' => 'Liberia',
		'LY' => 'Libyan Arab Jamahiriya',
		'LI' => 'Liechtenstein',
		'LT' => 'Lithuania',
		'LU' => 'Luxembourg',
		'MO' => 'Macao',
		'MK' => 'Macedonia',
		'MG' => 'Madagascar',
		'MW' => 'Malawi',
		'MY' => 'Malaysia',
		'MV' => 'Maldives',
		'ML' => 'Mali',
		'MT' => 'Malta',
		'MH' => 'Marshall Islands',
		'MQ' => 'Martinique',
		'MR' => 'Mauritania',
		'MU' => 'Mauritius',
		'YT' => 'Mayotte',
		'MX' => 'Mexico',
		'FM' => 'Micronesia, Federated States Of',
		'MD' => 'Moldova',
		'MC' => 'Monaco',
		'MN' => 'Mongolia',
		'ME' => 'Montenegro',
		'MS' => 'Montserrat',
		'MA' => 'Morocco',
		'MZ' => 'Mozambique',
		'MM' => 'Myanmar',
		'NA' => 'Namibia',
		'NR' => 'Nauru',
		'NP' => 'Nepal',
		'NL' => 'Netherlands',
		'AN' => 'Netherlands Antilles',
		'NC' => 'New Caledonia',
		'NZ' => 'New Zealand',
		'NI' => 'Nicaragua',
		'NE' => 'Niger',
		'NG' => 'Nigeria',
		'NU' => 'Niue',
		'NF' => 'Norfolk Island',
		'MP' => 'Northern Mariana Islands',
		'NO' => 'Norway',
		'OM' => 'Oman',
		'PK' => 'Pakistan',
		'PW' => 'Palau',
		'PS' => 'Palestinian Territory, Occupied',
		'PA' => 'Panama',
		'PG' => 'Papua New Guinea',
		'PY' => 'Paraguay',
		'PE' => 'Peru',
		'PH' => 'Philippines',
		'PN' => 'Pitcairn',
		'PL' => 'Poland',
		'PT' => 'Portugal',
		'PR' => 'Puerto Rico',
		'QA' => 'Qatar',
		'RE' => 'Reunion',
		'RO' => 'Romania',
		'RU' => 'Russian Federation',
		'RW' => 'Rwanda',
		'BL' => 'Saint Barthelemy',
		'SH' => 'Saint Helena',
		'KN' => 'Saint Kitts And Nevis',
		'LC' => 'Saint Lucia',
		'MF' => 'Saint Martin',
		'PM' => 'Saint Pierre And Miquelon',
		'VC' => 'Saint Vincent And Grenadines',
		'WS' => 'Samoa',
		'SM' => 'San Marino',
		'ST' => 'Sao Tome And Principe',
		'SA' => 'Saudi Arabia',
		'SN' => 'Senegal',
		'RS' => 'Serbia',
		'SC' => 'Seychelles',
		'SL' => 'Sierra Leone',
		'SG' => 'Singapore',
		'SK' => 'Slovakia',
		'SI' => 'Slovenia',
		'SB' => 'Solomon Islands',
		'SO' => 'Somalia',
		'ZA' => 'South Africa',
		'GS' => 'South Georgia And Sandwich Isl.',
		'ES' => 'Spain',
		'LK' => 'Sri Lanka',
		'SD' => 'Sudan',
		'SR' => 'Suriname',
		'SJ' => 'Svalbard And Jan Mayen',
		'SZ' => 'Swaziland',
		'SE' => 'Sweden',
		'CH' => 'Switzerland',
		'SY' => 'Syrian Arab Republic',
		'TW' => 'Taiwan',
		'TJ' => 'Tajikistan',
		'TZ' => 'Tanzania',
		'TH' => 'Thailand',
		'TL' => 'Timor-Leste',
		'TG' => 'Togo',
		'TK' => 'Tokelau',
		'TO' => 'Tonga',
		'TT' => 'Trinidad And Tobago',
		'TN' => 'Tunisia',
		'TR' => 'Turkey',
		'TM' => 'Turkmenistan',
		'TC' => 'Turks And Caicos Islands',
		'TV' => 'Tuvalu',
		'UG' => 'Uganda',
		'UA' => 'Ukraine',
		'AE' => 'United Arab Emirates',
		'GB' => 'United Kingdom',
		'US' => 'United States',
		'UM' => 'United States Outlying Islands',
		'UY' => 'Uruguay',
		'UZ' => 'Uzbekistan',
		'VU' => 'Vanuatu',
		'VE' => 'Venezuela',
		'VN' => 'Viet Nam',
		'VG' => 'Virgin Islands, British',
		'VI' => 'Virgin Islands, U.S.',
		'WF' => 'Wallis And Futuna',
		'EH' => 'Western Sahara',
		'YE' => 'Yemen',
		'ZM' => 'Zambia',
		'ZW' => 'Zimbabwe',
	);

    	public function __construct(Nette\Database\Context $database, \App\Model\Stats $stats)
    	{
    	    $this->database = $database;
    	    $this->stats = $stats;
    	}

	public function renderDefault()
	{
		$this->template->maxCountries = $this->maxCountries;
		#print_r($this->template->_POST['EDGE']);
		#https://github.com/nnnick/Chart.js
		#$t = $_POST['GPRS'];
		#echo "$t";
	}


	protected function createComponentCompareCountriesForm()
	{
		// Getting list of all existing iso country codes from AggregatedData table
		$aData = $this->database->table('AggregatedDataCountries');
		$countries = $aData->select('DISTINCT isoCountryCode')
			->order('isoCountryCode ASC')
			->fetchPairs('isoCountryCode', 'isoCountryCode');
		foreach($countries as $key => $countryCode) {
			$countries[$key] = $this->countries[strtoupper($countryCode)];
		}

		// Getting list of all existing radio technology types from AggregatedData table
		$aData = $this->database->table('AggregatedDataCountries');
		$radioTechList = $aData->select('DISTINCT radioTechnology')
			->order('radioTechnology ASC')
			->fetchPairs('radioTechnology', 'radioTechnology');

		$form = new UI\Form();
		$form->addSelect('tech', 'Technologie', $radioTechList);
		$form->addMultiSelect('country', 'Zem:', $countries)
			->setAttribute('size', 12);
		$form->addRadioList('stat', '', array('median' => 'Median', 'average' => 'Average'))->setDefaultValue('median');
		$form->addSubmit('submit', 'Draw Graph');
		$form->onSuccess[] = array($this, 'compareCountriesSuccess');

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

	protected function createComponentCompareOperatorsForm()
	{
		// Getting list of all existing iso country codes from AggregatedData table
		$aData = $this->database->table('AggregatedDataOperators');
		$countries = $aData->select('DISTINCT isoCountryCode')
			->order('isoCountryCode ASC')
			->fetchPairs('isoCountryCode', 'isoCountryCode');
		foreach($countries as $key => $countryCode) {
			$countries[$key] = $this->countries[strtoupper($countryCode)];
		}

		// Getting list of all existing radio technology types from AggregatedData table
		$aData = $this->database->table('AggregatedDataOperators');
		$radioTechList = $aData->select('DISTINCT radioTechnology')
			->order('radioTechnology ASC')
			->fetchPairs('radioTechnology', 'radioTechnology');

		$form = new UI\Form();
		$form->addCheckboxList('tech', 'Technologie', $radioTechList);
		$form->addSelect('country', 'Zem:', $countries);
		$form->addRadioList('stat', '', array('median' => 'Median', 'average' => 'Average'))->setDefaultValue('median');
		$form->addSubmit('submit', 'Draw Graph');
		$form->onSuccess[] = array($this, 'compareOperatorsSuccess');

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

	public function compareCountriesSuccess(UI\Form $form, $values)
	{
		$records = $this->stats->getDataCountries($values['country'], $values['tech']);
		
		$measures = array('DownloadSpeed', 'Latency', 'Qoe');
		foreach($measures as $measure) {
			$data = array(array('Technology'));
			foreach($values['country'] as $key => $country) {
				if ($key >= $this->maxCountries) {
					break;
				}
				$data[0][] = $this->countries[strtoupper($country)];
			}
			$line = array($values['tech']);
			foreach($records as $key => $record) {
				if ($key >= $this->maxCountries) {
					break;
				}
				if ($record->radTech != $values['tech']) {
					continue;
				} else {
					$line[] = ($values['stat'] == 'median')
						? $record->{'med'.$measure}
						: $record->{'avg'.$measure};
				}
			}
			$data[] = $line;
			$this->template->{'data'.$measure} = $data;
		}
		$this->template->graphTitle = 'Graphs comparing countries for radio technology ' .$values['tech'];
		$this->template->hAxisTitle = 'Countries per technology';
		$this->template->graphText = 'countries:';
		$this->template->measure = $values['stat'];

	}

	public function compareOperatorsSuccess(UI\Form $form, $values)
	{
		$records = $this->stats->getDataOperators(array($values['country']), $values['tech']);

		$measures = array('DownloadSpeed', 'Latency', 'Qoe');
		foreach($measures as $measure) {
			$data = array(array('Technology'));
			foreach($records as $record) {
				if (!in_array($record->operator, $data[0])) {
					$data[0][] = $record->operator;
				}
			}
			foreach($values['tech'] as $tech) {
				$line = array($tech);
				foreach($records as $record) {
					if ($record->radTech != $tech) {
						continue;
					} else {
						$line[] = ($values['stat'] == 'median')
							? $record->{'med'.$measure}
							: $record->{'avg'.$measure};
					}
				}
				$data[] = $line;
			}
			$this->template->{'data'.$measure} = $data;
		}
		$this->template->graphTitle = 'Graphs comparing operators from '.$this->countries[strtoupper($values['country'])];
		$this->template->hAxisTitle = 'Mobile operators per technology';
		$this->template->graphText = 'operators:';
		$this->template->measure = $values['stat'];
	}
}
