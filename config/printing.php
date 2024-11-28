<?php
return [
    'default' => 'local',
    'connections' => [
        'local' => [
            'host' => 'localhost',
            'port' => 631, // Default CUPS port
        ],
        'remote' => [
            'host' => 'remote-cups-server-ip', // Replace with your remote CUPS server IP
            'port' => 631, // Ensure this is the correct port
        ],
    ],
];
