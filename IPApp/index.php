<?php

Class Database{
    private $link;

    /**
     * Database constructor.
     */
    public function __construct()
    {

        $this->connect();

    }

    /**
     * @return $this
     */
    private function connect ()
    {
        $config = require_once 'config.php';

        $dsn = 'mysql:host ='.$config['host'].';dbname='.$config['db_name'].';charset='.$config['charset'];

        $this->link = new PDO($dsn, $config['username'], $config['password']);

        return $this;

    }

    /**
     * @param $sql
     * @return mixed
     */
    public function execute ($sql)
    {

        $sth = $this->link->prepare($sql);

        return $sth->execute();

    }

    /**
     * @param $sql
     * @return array
     */
    public function query($sql)
    {

        $exe = $this->link->prepare($sql);

        $exe->execute();

        $result = $exe->fetchAll(PDO::FETCH_ASSOC);

        if($result === false) {
            return [];
        }

        return $result;

    }
}

function getUserIP()
{
    $ch = curl_init('http://api.sypexgeo.net/');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $json = curl_exec($ch);
    curl_close($ch);

    $api_result = json_decode($json, true);

    $result = $api_result['ip'];

    return $result;
}

function getUserCountry($ip, $access_key = 'b75e0caeb051a34188ad7a1426b50abc')
{
    $ch = curl_init('http://api.ipapi.com/'.$ip.'?access_key='.$access_key.'');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $json = curl_exec($ch);
    curl_close($ch);

    $api_result = json_decode($json, true);

    $result = $api_result['country_name'];

    return $result;
}


// Initialize CURL:
$ch = curl_init('http://api.ipapi.com/'.$ip.'?access_key='.$access_key.'');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Store the data:
$json = curl_exec($ch);
curl_close($ch);

// Decode JSON response:
$api_result = json_decode($json, true);

$ip = getUserIP();
$country = getUserCountry($ip, $access_key = 'b75e0caeb051a34188ad7a1426b50abc');
//$ip ='213.111.90.220';
$access_key = 'b75e0caeb051a34188ad7a1426b50abc';
$db = new Database();
$all = $db->query("SELECT * FROM `tb_ip_addresses` WHERE ip ='".$ip."'");
if($all == true)
{
    $db->query("UPDATE `Test`.`tb_ip_addresses` SET `country` = '$country' ");
}
else
{
    $db->query("INSERT INTO `tb_ip_addresses` (`id`, `ip`, `country`) VALUES (NULL,'$ip', '$country')");
}

echo '<pre>';
print_r($ip);
echo '</pre>';
echo '<pre>';
print_r($country);
echo '</pre>';
