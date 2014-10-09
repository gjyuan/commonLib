<?php

return array(
    "mysql"=>array(
        "master"=>array("ip"=>"192.168.10.68", "port"=>"3306", 'user'=>"sns"),
        'slaver'=>array(
            array("ip"=>"192.168.10.68", "port"=>"3306", 'user'=>"sns"),
            array("ip"=>"192.168.10.68", "port"=>"3306", 'user'=>"sns"),
            ),
        'weight'=>array(0.4, 0.6)
    ),
    'redis'=>array(
        "master"=>array('ip'=>"192.168.10.68", 'port'=>'6538')
    )
    
);
