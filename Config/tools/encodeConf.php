<?php

$confFile = $argv[1];

$confString = file_get_contents($confFile);
if($confString === false) {
    die("Read $confFile failed.\n");
}

$confString = trim($confString);
$confString = ltrim($confString, "<?php");
$confString = rtrim($confString, "?>");

$conf = eval($confString);

$ret = "";
foreach($conf as $key => $value) {
    $value = json_encode($value);
    $value = trim($value, '"');
    //$value = preg_replace('/"/', '\"', $value);
    //$ret .= $key . ' = "' . $value . '"'."\n";
    $ret .= $key . " = $value \n";
}

echo $ret;
