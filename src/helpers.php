<?php

if(!function_exists('timezones')) {
	function timezones() {
		$tzs = \DateTimeZone::listIdentifiers();
		$items = array();
		
		foreach ($tzs as $key => $value) {
			$items[$value] = $value;
		}
		
		return $items;
	}
}

if(!function_exists('getCountriesArray')) {
	function getCountriesArray() {
		return [
			"AF" => "Afghanistan",
			"AX" => "Åland Islands",
			"AL" => "Albania",
			"DZ" => "Algeria",
			"AS" => "American Samoa",
			"AD" => "Andorra",
			"AO" => "Angola",
			"AI" => "Anguilla",
			"AQ" => "Antarctica",
			"AG" => "Antigua and Barbuda",
			"AR" => "Argentina",
			"AM" => "Armenia",
			"AW" => "Aruba",
			"AU" => "Australia",
			"AT" => "Austria",
			"AZ" => "Azerbaijan",
			"BS" => "Bahamas",
			"BH" => "Bahrain",
			"BD" => "Bangladesh",
			"BB" => "Barbados",
			"BY" => "Belarus",
			"BE" => "Belgium",
			"BZ" => "Belize",
			"BJ" => "Benin",
			"BM" => "Bermuda",
			"BT" => "Bhutan",
			"BO" => "Bolivia, Plurinational State of",
			"BQ" => "Bonaire, Sint Eustatius and Saba",
			"BA" => "Bosnia and Herzegovina",
			"BW" => "Botswana",
			"BV" => "Bouvet Island",
			"BR" => "Brazil",
			"IO" => "British Indian Ocean Territory",
			"BN" => "Brunei Darussalam",
			"BG" => "Bulgaria",
			"BF" => "Burkina Faso",
			"BI" => "Burundi",
			"KH" => "Cambodia",
			"CM" => "Cameroon",
			"CA" => "Canada",
			"CV" => "Cape Verde",
			"KY" => "Cayman Islands",
			"CF" => "Central African Republic",
			"TD" => "Chad",
			"CL" => "Chile",
			"CN" => "China",
			"CX" => "Christmas Island",
			"CC" => "Cocos (Keeling) Islands",
			"CO" => "Colombia",
			"KM" => "Comoros",
			"CG" => "Congo",
			"CD" => "Congo, the Democratic Republic of the",
			"CK" => "Cook Islands",
			"CR" => "Costa Rica",
			"CI" => "Côte d'Ivoire",
			"HR" => "Croatia",
			"CU" => "Cuba",
			"CW" => "Curaçao",
			"CY" => "Cyprus",
			"CZ" => "Czech Republic",
			"DK" => "Denmark",
			"DJ" => "Djibouti",
			"DM" => "Dominica",
			"DO" => "Dominican Republic",
			"EC" => "Ecuador",
			"EG" => "Egypt",
			"SV" => "El Salvador",
			"GQ" => "Equatorial Guinea",
			"ER" => "Eritrea",
			"EE" => "Estonia",
			"ET" => "Ethiopia",
			"FK" => "Falkland Islands (Malvinas",
			"FO" => "Faroe Islands",
			"FJ" => "Fiji",
			"FI" => "Finland",
			"FR" => "France",
			"GF" => "French Guiana",
			"PF" => "French Polynesia",
			"TF" => "French Southern Territories",
			"GA" => "Gabon",
			"GM" => "Gambia",
			"GE" => "Georgia",
			"DE" => "Germany",
			"GH" => "Ghana",
			"GI" => "Gibraltar",
			"GR" => "Greece",
			"GL" => "Greenland",
			"GD" => "Grenada",
			"GP" => "Guadeloupe",
			"GU" => "Guam",
			"GT" => "Guatemala",
			"GG" => "Guernsey",
			"GN" => "Guinea",
			"GW" => "Guinea-Bissau",
			"GY" => "Guyana",
			"HT" => "Haiti",
			"HM" => "Heard Island and McDonald Islands",
			"VA" => "Holy See (Vatican City State",
			"HN" => "Honduras",
			"HK" => "Hong Kong",
			"HU" => "Hungary",
			"IS" => "Iceland",
			"IN" => "India",
			"ID" => "Indonesia",
			"IR" => "Iran, Islamic Republic of",
			"IQ" => "Iraq",
			"IE" => "Ireland",
			"IM" => "Isle of Man",
			"IL" => "Israel",
			"IT" => "Italy",
			"JM" => "Jamaica",
			"JP" => "Japan",
			"JE" => "Jersey",
			"JO" => "Jordan",
			"KZ" => "Kazakhstan",
			"KE" => "Kenya",
			"KI" => "Kiribati",
			"KP" => "Korea, Democratic People's Republic of",
			"KR" => "Korea, Republic of",
			"KW" => "Kuwait",
			"KG" => "Kyrgyzstan",
			"LA" => "Lao People's Democratic Republic",
			"LV" => "Latvia",
			"LB" => "Lebanon",
			"LS" => "Lesotho",
			"LR" => "Liberia",
			"LY" => "Libya",
			"LI" => "Liechtenstein",
			"LT" => "Lithuania",
			"LU" => "Luxembourg",
			"MO" => "Macao",
			"MK" => "Macedonia, the former Yugoslav Republic of",
			"MG" => "Madagascar",
			"MW" => "Malawi",
			"MY" => "Malaysia",
			"MV" => "Maldives",
			"ML" => "Mali",
			"MT" => "Malta",
			"MH" => "Marshall Islands",
			"MQ" => "Martinique",
			"MR" => "Mauritania",
			"MU" => "Mauritius",
			"YT" => "Mayotte",
			"MX" => "Mexico",
			"FM" => "Micronesia, Federated States of",
			"MD" => "Moldova, Republic of",
			"MC" => "Monaco",
			"MN" => "Mongolia",
			"ME" => "Montenegro",
			"MS" => "Montserrat",
			"MA" => "Morocco",
			"MZ" => "Mozambique",
			"MM" => "Myanmar",
			"NA" => "Namibia",
			"NR" => "Nauru",
			"NP" => "Nepal",
			"NL" => "Netherlands",
			"NC" => "New Caledonia",
			"NZ" => "New Zealand",
			"NI" => "Nicaragua",
			"NE" => "Niger",
			"NG" => "Nigeria",
			"NU" => "Niue",
			"NF" => "Norfolk Island",
			"MP" => "Northern Mariana Islands",
			"NO" => "Norway",
			"OM" => "Oman",
			"PK" => "Pakistan",
			"PW" => "Palau",
			"PS" => "Palestinian Territory, Occupied",
			"PA" => "Panama",
			"PG" => "Papua New Guinea",
			"PY" => "Paraguay",
			"PE" => "Peru",
			"PH" => "Philippines",
			"PN" => "Pitcairn",
			"PL" => "Poland",
			"PT" => "Portugal",
			"PR" => "Puerto Rico",
			"QA" => "Qatar",
			"RE" => "Réunion",
			"RO" => "Romania",
			"RU" => "Russian Federation",
			"RW" => "Rwanda",
			"BL" => "Saint Barthélemy",
			"SH" => "Saint Helena, Ascension and Tristan da Cunha",
			"KN" => "Saint Kitts and Nevis",
			"LC" => "Saint Lucia",
			"MF" => "Saint Martin (French part",
			"PM" => "Saint Pierre and Miquelon",
			"VC" => "Saint Vincent and the Grenadines",
			"WS" => "Samoa",
			"SM" => "San Marino",
			"ST" => "Sao Tome and Principe",
			"SA" => "Saudi Arabia",
			"SN" => "Senegal",
			"RS" => "Serbia",
			"SC" => "Seychelles",
			"SL" => "Sierra Leone",
			"SG" => "Singapore",
			"SX" => "Sint Maarten (Dutch part",
			"SK" => "Slovakia",
			"SI" => "Slovenia",
			"SB" => "Solomon Islands",
			"SO" => "Somalia",
			"ZA" => "South Africa",
			"GS" => "South Georgia and the South Sandwich Islands",
			"SS" => "South Sudan",
			"ES" => "Spain",
			"LK" => "Sri Lanka",
			"SD" => "Sudan",
			"SR" => "Suriname",
			"SJ" => "Svalbard and Jan Mayen",
			"SZ" => "Swaziland",
			"SE" => "Sweden",
			"CH" => "Switzerland",
			"SY" => "Syrian Arab Republic",
			"TW" => "Taiwan, Province of China",
			"TJ" => "Tajikistan",
			"TZ" => "Tanzania, United Republic of",
			"TH" => "Thailand",
			"TL" => "Timor-Leste",
			"TG" => "Togo",
			"TK" => "Tokelau",
			"TO" => "Tonga",
			"TT" => "Trinidad and Tobago",
			"TN" => "Tunisia",
			"TR" => "Turkey",
			"TM" => "Turkmenistan",
			"TC" => "Turks and Caicos Islands",
			"TV" => "Tuvalu",
			"UG" => "Uganda",
			"UA" => "Ukraine",
			"AE" => "United Arab Emirates",
			"GB" => "United Kingdom",
			"US" => "United States",
			"UM" => "United States Minor Outlying Islands",
			"UY" => "Uruguay",
			"UZ" => "Uzbekistan",
			"VU" => "Vanuatu",
			"VE" => "Venezuela, Bolivarian Republic of",
			"VN" => "Viet Nam",
			"VG" => "Virgin Islands, British",
			"VI" => "Virgin Islands, U.S",
			"WF" => "Wallis and Futuna",
			"EH" => "Western Sahara",
			"YE" => "Yemen",
			"ZM" => "Zambia",
			"ZW" => "Zimbabwe",
		];
	}
}

if(!function_exists('gstToStateCode')) {
	function gstToStateCode($gstin) {
		
		$gstStateCodes = [
			'37' => 'AD',
			'12' => 'AR',
			'18' => 'AS',
			'10' => 'BR',
			'22' => 'CG',
			'07' => 'DL',
			'30' => 'GA',
			'24' => 'GJ',
			'06' => 'HR',
			'02' => 'HP',
			'01' => 'JK',
			'20' => 'JH',
			'29' => 'KA',
			'32' => 'KL',
			'31' => 'LD',
			'23' => 'MP',
			'27' => 'MH',
			'14' => 'MN',
			'17' => 'ML',
			'15' => 'MZ',
			'13' => 'NL',
			'21' => 'OD',
			'34' => 'PY',
			'03' => 'PB',
			'08' => 'RJ',
			'11' => 'SK',
			'33' => 'TN',
			'36' => 'TS',
			'16' => 'TR',
			'09' => 'UP',
			'05' => 'UK',
			'19' => 'WB',
			'35' => 'AN',
			'04' => 'CH',
			'26' => 'DNHDD',
			'38' => 'LA',
			'97' => 'OT',
		];
		

		$code = substr($gstin, 0, 2);
		
		return $gstStateCodes[$code];

	}
}

if(!function_exists('getCurrenciesArray')) {
	function getCurrenciesArray() {

		return [
			"AED" => "(AED) United Arab Emirates dirham",
			"AFN" => "(AFN) Afghan afghani",
			"ALL" => "(ALL) Albanian lek",
			"AMD" => "(AMD) Armenian dram",
			"ANG" => "(ANG) Netherlands Antillean guilder",
			"AOA" => "(AOA) Angolan kwanza",
			"ARS" => "(ARS) Argentine peso",
			"AUD" => "(AUD) Australian dollar",
			"AWG" => "(AWG) Aruban florin",
			"AZN" => "(AZN) Azerbaijani manat",
			"BAM" => "(BAM) Bosnia and Herzegovina convertible mark",
			"BBD" => "(BBD) Barbados dollar",
			"BDT" => "(BDT) Bangladeshi taka",
			"BGN" => "(BGN) Bulgarian lev",
			"BHD" => "(BHD) Bahraini dinar",
			"BIF" => "(BIF) Burundian franc",
			"BMD" => "(BMD) Bermudian dollar",
			"BND" => "(BND) Brunei dollar",
			"BOB" => "(BOB) Boliviano",
			"BRL" => "(BRL) Brazilian real",
			"BSD" => "(BSD) Bahamian dollar",
			"BTN" => "(BTN) Bhutanese ngultrum",
			"BWP" => "(BWP) Botswana pula",
			"BYN" => "(BYN) New Belarusian ruble",
			"BYR" => "(BYR) Belarusian ruble",
			"BZD" => "(BZD) Belize dollar",
			"CAD" => "(CAD) Canadian dollar",
			"CDF" => "(CDF) Congolese franc",
			"CHF" => "(CHF) Swiss franc",
			"CLF" => "(CLF) Unidad de Fomento",
			"CLP" => "(CLP) Chilean peso",
			"CNY" => "(CNY) Renminbi|Chinese yuan",
			"COP" => "(COP) Colombian peso",
			"CRC" => "(CRC) Costa Rican colon",
			"CUC" => "(CUC) Cuban convertible peso",
			"CUP" => "(CUP) Cuban peso",
			"CVE" => "(CVE) Cape Verde escudo",
			"CZK" => "(CZK) Czech koruna",
			"DJF" => "(DJF) Djiboutian franc",
			"DKK" => "(DKK) Danish krone",
			"DOP" => "(DOP) Dominican peso",
			"DZD" => "(DZD) Algerian dinar",
			"EGP" => "(EGP) Egyptian pound",
			"ERN" => "(ERN) Eritrean nakfa",
			"ETB" => "(ETB) Ethiopian birr",
			"EUR" => "(EUR) Euro",
			"FJD" => "(FJD) Fiji dollar",
			"FKP" => "(FKP) Falkland Islands pound",
			"GBP" => "(GBP) Pound sterling",
			"GEL" => "(GEL) Georgian lari",
			"GHS" => "(GHS) Ghanaian cedi",
			"GIP" => "(GIP) Gibraltar pound",
			"GMD" => "(GMD) Gambian dalasi",
			"GNF" => "(GNF) Guinean franc",
			"GTQ" => "(GTQ) Guatemalan quetzal",
			"GYD" => "(GYD) Guyanese dollar",
			"HKD" => "(HKD) Hong Kong dollar",
			"HNL" => "(HNL) Honduran lempira",
			"HRK" => "(HRK) Croatian kuna",
			"HTG" => "(HTG) Haitian gourde",
			"HUF" => "(HUF) Hungarian forint",
			"IDR" => "(IDR) Indonesian rupiah",
			"ILS" => "(ILS) Israeli new shekel",
			"INR" => "(INR) Indian rupee",
			"IQD" => "(IQD) Iraqi dinar",
			"IRR" => "(IRR) Iranian rial",
			"ISK" => "(ISK) Icelandic króna",
			"JMD" => "(JMD) Jamaican dollar",
			"JOD" => "(JOD) Jordanian dinar",
			"JPY" => "(JPY) Japanese yen",
			"KES" => "(KES) Kenyan shilling",
			"KGS" => "(KGS) Kyrgyzstani som",
			"KHR" => "(KHR) Cambodian riel",
			"KMF" => "(KMF) Comoro franc",
			"KPW" => "(KPW) North Korean won",
			"KRW" => "(KRW) South Korean won",
			"KWD" => "(KWD) Kuwaiti dinar",
			"KYD" => "(KYD) Cayman Islands dollar",
			"KZT" => "(KZT) Kazakhstani tenge",
			"LAK" => "(LAK) Lao kip",
			"LBP" => "(LBP) Lebanese pound",
			"LKR" => "(LKR) Sri Lankan rupee",
			"LRD" => "(LRD) Liberian dollar",
			"LSL" => "(LSL) Lesotho loti",
			"LYD" => "(LYD) Libyan dinar",
			"MAD" => "(MAD) Moroccan dirham",
			"MDL" => "(MDL) Moldovan leu",
			"MGA" => "(MGA) Malagasy ariary",
			"MKD" => "(MKD) Macedonian denar",
			"MMK" => "(MMK) Myanmar kyat",
			"MNT" => "(MNT) Mongolian tögrög",
			"MOP" => "(MOP) Macanese pataca",
			"MRO" => "(MRO) Mauritanian ouguiya",
			"MUR" => "(MUR) Mauritian rupee",
			"MVR" => "(MVR) Maldivian rufiyaa",
			"MWK" => "(MWK) Malawian kwacha",
			"MXN" => "(MXN) Mexican peso",
			"MXV" => "(MXV) Mexican Unidad de Inversion",
			"MYR" => "(MYR) Malaysian ringgit",
			"MZN" => "(MZN) Mozambican metical",
			"NAD" => "(NAD) Namibian dollar",
			"NGN" => "(NGN) Nigerian naira",
			"NIO" => "(NIO) Nicaraguan córdoba",
			"NOK" => "(NOK) Norwegian krone",
			"NPR" => "(NPR) Nepalese rupee",
			"NZD" => "(NZD) New Zealand dollar",
			"OMR" => "(OMR) Omani rial",
			"PAB" => "(PAB) Panamanian balboa",
			"PEN" => "(PEN) Peruvian Sol",
			"PGK" => "(PGK) Papua New Guinean kina",
			"PHP" => "(PHP) Philippine peso",
			"PKR" => "(PKR) Pakistani rupee",
			"PLN" => "(PLN) Polish złoty",
			"PYG" => "(PYG) Paraguayan guaraní",
			"QAR" => "(QAR) Qatari riyal",
			"RON" => "(RON) Romanian leu",
			"RSD" => "(RSD) Serbian dinar",
			"RUB" => "(RUB) Russian ruble",
			"RWF" => "(RWF) Rwandan franc",
			"SAR" => "(SAR) Saudi riyal",
			"SBD" => "(SBD) Solomon Islands dollar",
			"SCR" => "(SCR) Seychelles rupee",
			"SDG" => "(SDG) Sudanese pound",
			"SEK" => "(SEK) Swedish krona",
			"SGD" => "(SGD) Singapore dollar",
			"SHP" => "(SHP) Saint Helena pound",
			"SLL" => "(SLL) Sierra Leonean leone",
			"SOS" => "(SOS) Somali shilling",
			"SRD" => "(SRD) Surinamese dollar",
			"SSP" => "(SSP) South Sudanese pound",
			"STD" => "(STD) São Tomé and Príncipe dobra",
			"SVC" => "(SVC) Salvadoran colón",
			"SYP" => "(SYP) Syrian pound",
			"SZL" => "(SZL) Swazi lilangeni",
			"THB" => "(THB) Thai baht",
			"TJS" => "(TJS) Tajikistani somoni",
			"TMT" => "(TMT) Turkmenistani manat",
			"TND" => "(TND) Tunisian dinar",
			"TOP" => "(TOP) Tongan paʻanga",
			"TRY" => "(TRY) Turkish lira",
			"TTD" => "(TTD) Trinidad and Tobago dollar",
			"TWD" => "(TWD) New Taiwan dollar",
			"TZS" => "(TZS) Tanzanian shilling",
			"UAH" => "(UAH) Ukrainian hryvnia",
			"UGX" => "(UGX) Ugandan shilling",
			"USD" => "(USD) United States dollar",
			"UYI" => "(UYI) Uruguay Peso en Unidades Indexadas",
			"UYU" => "(UYU) Uruguayan peso",
			"UZS" => "(UZS) Uzbekistan som",
			"VEF" => "(VEF) Venezuelan bolívar",
			"VND" => "(VND) Vietnamese đồng",
			"VUV" => "(VUV) Vanuatu vatu",
			"WST" => "(WST) Samoan tala",
			"XAF" => "(XAF) Central African CFA franc",
			"XCD" => "(XCD) East Caribbean dollar",
			"XOF" => "(XOF) West African CFA franc",
			"XPF" => "(XPF) CFP franc",
			"XXX" => "(XXX) No currency",
			"YER" => "(YER) Yemeni rial",
			"ZAR" => "(ZAR) South African rand",
			"ZMW" => "(ZMW) Zambian kwacha",
			"ZWL" => "(ZWL) Zimbabwean dollar"
		];

	}
}

if(!function_exists('getIndiaStateCodesArray')) {

	function getIndiaStateCodesArray() {

		return [
			'AP' => 'Andhra Pradesh',
			'AR' => 'Arunachal Pradesh',
			'AS' => 'Assam',
			'BR' => 'Bihar',
			'CT' => 'Chhattisgarh',
			'DL' => 'Delhi',
			'GA' => 'Goa',
			'GJ' => 'Gujarat',
			'HR' => 'Haryana',
			'HP' => 'Himachal Pradesh',
			'JK' => 'Jammu and Kashmir',
			'JH' => 'Jharkhand',
			'KA' => 'Karnataka',
			'KL' => 'Kerala',
			'MP' => 'Madhya Pradesh',
			'MH' => 'Maharashtra',
			'MN' => 'Manipur',
			'ML' => 'Meghalaya',
			'MZ' => 'Mizoram',
			'NL' => 'Nagaland',
			'OR' => 'Odisha',
			'PB' => 'Punjab',
			'RJ' => 'Rajasthan',
			'SK' => 'Sikkim',
			'TN' => 'Tamil Nadu',
			'TG' => 'Telangana',
			'TR' => 'Tripura',
			'UP' => 'Uttar Pradesh',
			'UT' => 'Uttarakhand',
			'WB' => 'West Bengal',
			'AN' => 'Andaman and Nicobar Islands',
			'CH' => 'Chandigarh',
			'DN' => 'Dadra and Nagar Haveli',
			'DD' => 'Daman and Diu',
			'LD' => 'Lakshadweep',
			'PY' => 'Puducherry'
		];
	}
}