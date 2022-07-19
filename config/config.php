<?php
return [
	'default'=>'',
	'drivers' => [
		'cryun' => [
            'clazz' => 'Cryun',
            'options' => [
                'accesskey' => '',
                'secret' => '',
                'templates' => [],
            ]
		],
		'mandao' => [
            'clazz' => 'Mandao',
            'options' => [
                'url' => '',
                'port' => null,
                'sn' => '',
                'secret' => ''
            ]
		]
	]
];