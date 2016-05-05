<?php

namespace App\Presenters;

use Nette;
use Nette\Database\Context;
use Nette\Application\UI;


class HomepagePresenter extends Nette\Application\UI\Presenter
{
	/** @var maxCountries variable specify how many countries can be selected for Country graph */
	protected $maxCountries = 10;
    	/** @var Nette\Database\Context */
    	private $database;
    	/** @var \App\Model\Stats */
    	private $stats;
	/** @var countries contain Iso Country Codes and english names for each country */
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

	// Function to generate data for selection in form for Country graphs
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

		// Create form with Select and multiselect
		$form = new UI\Form();
		$form->addSelect('tech', 'Technologie', $radioTechList);
		$form->addMultiSelect('country', 'Zem:', $countries)
			->setAttribute('size', 12);
		$form->addRadioList('stat', '', array('median' => 'Median', 'average' => 'Average'))->setDefaultValue('median');
		$form->addSubmit('submit', 'Draw Graph');
		$form->onSuccess[] = array($this, 'compareCountriesSuccess');

		// setup selection form renderrling
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

	// Function to generate data for selection in form for Operators graphs
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

		// Create form with Select and multiselect
		$form = new UI\Form();
		$form->addCheckboxList('tech', 'Technologie', $radioTechList);
		$form->addSelect('country', 'Zem:', $countries);
		$form->addRadioList('stat', '', array('median' => 'Median', 'average' => 'Average'))->setDefaultValue('median');
		$form->addSubmit('submit', 'Draw Graph');
		$form->onSuccess[] = array($this, 'compareOperatorsSuccess');

		// setup operator form rendering
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

	/** Function that receives selected value from web form for Countries
	 get data from DB and provide those in array to javascript in Latte for graphs */
	public function compareCountriesSuccess(UI\Form $form, $values)
	{
		// Load data from AggregatedDataOperators table via stats service for countries and technologies from select form
		$records = $this->stats->getDataCountries($values['country'], $values['tech']);
	
		// $measures is array on which we iterate average/med values	
		$measures = array('DownloadSpeed', 'Latency', 'Qoe');
		foreach($measures as $measure) {
			// JS graphs have AxB matrix array where first line contain Country
			$data = array(array('Technology'));
			foreach($values['country'] as $key => $country) {
				if ($key >= $this->maxCountries) {
					break;
				}
				$data[0][] = $this->countries[strtoupper($country)];
			}
			/** JS graphs have AxB matrix array where each next line contain technology name 
                        and then values of avg/med for each operator in order how operators are add in first line of matrix */	
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

		// Setting text strings to be used in graphs and differs per average and median
		$this->template->graphTitle = 'Graphs comparing countries for radio technology ' .$values['tech'];
		$this->template->hAxisTitle = 'Countries per technology';
		$this->template->graphText = 'countries:';
		$this->template->measure = $values['stat'];
		// Setting minimal value from which will Y axis start. It is minimal|non-zero value - 20% of this value
		$vAxisMin = 10;
                $method = ($values['stat'] == 'median')
                        ? 'medQoe'
                        : 'avgQoe';

                foreach ($records as $line) { 
                        if ($line->$method != 0) {
                                if ($line->$method <= $vAxisMin) { $vAxisMin = $line->$method; }
                        }
                }
                $vAxisMin = $vAxisMin - $vAxisMin * 0.2;
                $this->template->vAxisMin = $vAxisMin;
	}

	/** Function that receives selected value from web form for Operators
         get data from DB and provide those in array to javascript in Latte for graphs */
	public function compareOperatorsSuccess(UI\Form $form, $values)
	{
		// Load data from AggregatedDataOperators table via stats service for countries and technologies from select form
		$records = $this->stats->getDataOperators(array($values['country']), $values['tech']);

		// $measures is array on which we iterate average/med values
		$measures = array('DownloadSpeed', 'Latency', 'Qoe');

		// Main loop to read data from DB and convert it to array format for JS graphs
		foreach($measures as $measure) {
			// JS graphs have AxB matrix array where first line contain Operators
			$data = array(array('Technology'));
			foreach($records as $record) {
				if (!in_array($record->operator, $data[0])) {
					$data[0][] = $record->operator;
				}
			}
			/** JS graphs have AxB matrix array where each next line contain technology name 
			and then values of avg/med for each operator in order how operators are add in first line of matrix */
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

		// Setting text strings to be used in graphs and differs per average and median
		$this->template->graphTitle = 'Graphs comparing operators from '.$this->countries[strtoupper($values['country'])];
		$this->template->hAxisTitle = 'Mobile operators per technology';
		$this->template->graphText = 'operators:';
		$this->template->measure = $values['stat'];
		// Setting minimal value from which will Y axis start. It is minimal|non-zero value - 20% of this value.
		$vAxisMin = 10;
		$method = ($values['stat'] == 'median')
		  	? 'medQoe'
  			: 'avgQoe';

		foreach ($records as $line) { 
			if ($line->$method != 0) {
				if ($line->$method <= $vAxisMin) { $vAxisMin = $line->$method; }
			}
		}
		$vAxisMin = $vAxisMin - $vAxisMin * 0.2;
		$this->template->vAxisMin = $vAxisMin;
	}
}
