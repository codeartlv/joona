<?php

return [
	'accepted'             => ' :attribute ir jābūt pieņemtam.',
	'active_url'           => ' :attribute ir ar nederīgu saiti.',
	'after'                => ' :attribute ir jābūt ar datumu pēc :datums.',
	'after_or_equal'       => ' :attribute ir jābūt ar datumu pēc vai vienādu ar :datums.',
	'alpha'                => ' :attribute var saturēt tikai burtus.',
	'alpha_dash'           => ' :attribute var saturēt tikai burtus, ciparus un atstarpes.',
	'alpha_num'            => ' :attribute var tikai saturēt burtus un ciparus.',
	'array'                => ' :attribute ir jābūt masīvam.',
	'before'               => ' :attribute ir jābūt ar datumu pirms :datums.',
	'before_or_equal'      => ' :attribute ir jābūt ar datumu pirms vai vienādu ar :datums.',
	'between'              => [
		'numeric' => ' :attribute jābūt starp :min un :max.',
		'file'    => ' :attribute jābūt starp :min un :max kilobaiti.',
		'string'  => ' :attribute jābūt no :min līdz :max zīmēm.',
		'array'   => ' :attribute jābūt no :min līdz :max vienībām.',
	],
	'boolean'              => ' :attribute laukam jābūt atbilstošam vai neatbilstošam.',
	'confirmed'            => ' :attribute apstiprinājums neatbilst.',
	'date'                 => ' :attribute nav derīgs datums.',
	'date_format'          => ' :attribute neatbilst formātam :format.',
	'different'            => ' :attribute un :other ir jābūt atšķirīgam.',
	'digits'               => ' :attribute ir jābūt :digits ciparam.',
	'digits_between'       => ' :attribute ir jābūt starp :min un :max ciparam.',
	'dimensions'           => ' :attribute ir nederīgs attēla izmērs.',
	'distinct'             => ' :attribute laukam ir dubulta vērtība.',
	'email'                => 'Laukā jānorāda derīga e-pasta adrese.',
	'exists'               => 'Izvēlētais :attribute ir nederīgs.',
	'file'                 => ' :attribute jābūt failam.',
	'filled'               => ':attribute lauks ir nepieciešams.',
	'gt'                   => [
		'numeric' => ':attribute jābūt lielākam par :value.',
		'file'    => ':attribute jābūt lielākam par :value kilobaiti.',
		'string'  => ':attribute jābūt garākam par :value zīmēm.',
		'array'   => ':attribute jāsatur vairāk kā :value vērtības.',
	],
	'gte'                  => [
		'numeric' => ':attribute jābūt lielākam vai vienādam par :value.',
		'file'    => ':attribute jābūt lielākam vai vienādam par :value kilobaitiem.',
		'string'  => ':attribute garumam jābūt vismaz vai vienādam ar :value zīmēm.',
		'array'   => ':attribute jāsatur vismaz :value vērtības.',
	],
	'image'                => ' :attribute jābūt attēlam.',
	'in'                   => 'Izvēlētais :attribute ir nederīgs.',
	'in_array'             => ' :attribute laiks neeksistē :cits.',
	'integer'              => ' :attribute ir jabūt skaitim.',
	'ip'                   => ' :attribute jābūt derīgai IP adresei.',
	'ipv4'                 => ':attribute jābūt derīgai IPv4 adresei.',
	'ipv6'                 => ':attribute jābūt derīgai IPv6 adresei.',
	'json'                 => ' :attribute jābūt derīgai JSON virknei.',
	'lt'                   => [
		'numeric' => ':attribute jābūt mazākam par :value.',
		'file'    => ':attribute jābūt mazākam par :value kilobaitiem.',
		'string'  => ':attribute garumam jābūt mazakam par :value zīmēm.',
		'array'   => ':attribute jāsatur mazāk kā :value vērtības.',
	],
	'lte'                  => [
		'numeric' => ':attribute jābūt mazāk vai vienādam ar :value.',
		'file'    => ':attribute jābūt mazāk vai vienādam ar :value kilobaitiem.',
		'string'  => ':attribute garumam jābūt ne vairāk kā :value zīmes.',
		'array'   => ':attribute jāsatur ne vairāk kā :value vērtības.',
	],
	'max'                  => [
		'numeric' => ' :attribute nedrīkst pārsniegt :max.',
		'file'    => ' :attribute nedrīkst pārsniegt :max kilobaiti.',
		'string'  => ' Lauka garums nedrīkst pārsniegt :max zīmes.',
		'array'   => ' :attribute nedrīkst pārsniegt :max vienības.',
	],
	'mimes'                => ' :attribute jābūt faila tipam: :values',
	'mimetypes'            => ' :attribute jābūt faile tipam: :values.',
	'min'                  => [
		'numeric' => ' :attribute jābūt vismaz :min.',
		'file'    => ' :attribute jābūt vismaz :min kilobaiti.',
		'string'  => ' :attribute jābūt vismaz :min zīmes.',
		'array'   => ' :attribute jāsatur vismaz :min vienības.',
	],
	'not_in'               => ' izvēlētais :attribute ir nederīgs.',
	'not_regex'            => ':attribute formāts ir neatbilstošs.',
	'numeric'              => ' :attribute jābūt skaitlim.',
	'password_hint' => [
		'uppercase' => 'Lielie burti',
		'lowercase' => 'Mazie burti',
		'symbols' => 'Simboli',
		'numbers' => 'Cipari',
		'length' => 'Garums vismaz :length',
		'long' => 'Ļoti gara parole',
		'mixed' => 'Lielie un mazie burti',
	],
	'password' => [
		'no_match' => 'Nekorekta esošā parole.',
		'unsecure' => 'Ievadītā parole nav pietiekami droša.',
	],
	'present'              => ' :attribute vērtībai jābūt norādītai.',
	'regex'                => ' :attribute formāts ir nederīgs.',
	'required'             => 'Šis lauks ir obligāti jāaizpilda.',
	'required_if'          => ' :attribute laukums ir nepieciešams, ja vien :other ir :values.',
	'required_unless'      => ' :attribute laukums ir nepieciešams, ja vien :other ir :values.',
	'required_with'        => ' :attribute laukums ir nepieciešams, kad :values ir pieejama.',
	'required_with_all'    => ' :attribute laukums ir nepieciešams, kad :values ir pieejama.',
	'required_without'     => ' :attribute laukums ir nepieciešams, kad :values nav pieejama.',
	'required_without_all' => ' :attribute laukums ir nepieciešams, kad neviena no :values nav pieejama.',
	'same'                 => ' :attribute un :citiem ir jāsakrīt.',
	'size'                 => [
		'numeric' => ' :attribute jābūt :size.',
		'file'    => ' :attribute jābūt :size kilobaiti.',
		'string'  => ' :attribute jābūt :size zīmes.',
		'array'   => ' :attribute jāsatur :size vienības.',
	],
	'string'               => ' :attribute jābūt virknē.',
	'timezone'             => ' :attribute jābūt derīgā zonā.',
	'unique'               => ' :attribute jau ir aizņemts.',
	'uploaded'             => ' :attribute netika augšuplādēts.',
	'url'                  => ' :attribute formāts ir nederīgs.',

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute.rule" to name the lines. This makes it quick to
	| specify a specific custom language line for a given attribute rule.
	|
	*/

	'custom' => [
		'attribute-name' => [
			'rule-name' => 'ziņa pēc pieprasījuma',
		],
	],

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Attributes
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap attribute place-holders
	| with something more reader friendly such as E-Mail Address instead
	| of "email". This simply helps us make messages a little cleaner.
	|
	*/

	'attributes' => [
		'username' => 'Lietotājvārds',
	],
];
