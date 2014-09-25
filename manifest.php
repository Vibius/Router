<?php return [
    
    "vibius" => true,

    "components" => [
    
        "Router" => [
            "alias" => "router",
            "provider" => "Vibius\Router\Router"
        ],
		
        "Server" => [
            "alias" => "server",
            "provider" => 'Vibius\Router\ServerData($_SERVER)'
        ],

		"Request" => [
            "alias" => "request",
            "provider" => "Vibius\Router\RequestData"
        ],

		"RequestParser" => [
            "alias" => "requestparser",
            "provider" => 'Vibius\Router\RequestParser'
        ]
    ]

];