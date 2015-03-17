<?php
error_reporting(E_ALL & ~E_NOTICE);
ini_set('log_errors', 'On');
ini_set('error_log', dirname(__FILE__) . '/_log/error.log');
set_time_limit(0);

/**
 * Class PackageProvider
 */
class PackageProvider
{
    public $options;
    public $repos;

    /**
     *
     */
    function __construct()
    {
        $basePath = dirname(__FILE__) . '/';

        $this->options = array(
            'basePath' => $basePath,
            'packagePath' => $basePath . '_packages/'
        );
        $this->repos = array();
    }

    /**
     * @param $v
     * @param $def
     * @return mixed
     */
    function ifnull($v, $def)
    {
        return $v ? $v : $def;
    }

    /**
     * @param $i
     * @param $p
     * @param bool $selected
     */
    function write_package($i, $p, $selected = false)
    {
        echo "<package>";
        echo "<id>deadbeefa00000000000000$i</id>";
        echo "<package>deadbeefb00000000000000$i</package>";
        echo "<name>" . $this->ifnull($p["displayName"], $p["name"]) . "</name>";
        echo "<display_name>" . $p["name"] . "</display_name>";
        echo "<version>" . $p["version"] . "</version>";
        echo "<version_major>" . $p["version_major"] . "</version_major>";
        echo "<version_minor>" . $p["version_minor"] . "</version_minor>";
        echo "<version_patch>" . $p["version_patch"] . "</version_patch>";
        echo "<vrelease>" . $p["version_release"] . "</vrelease>";
        echo "<vrelease_index/>";
        echo "<author>" . $this->ifnull($p["author"], "unknown") . "</author>";
        echo "<description>" . htmlspecialchars($this->ifnull($p["description"], "No description for this package")) . "</description>";
        echo "<instructions>" . htmlspecialchars($this->ifnull($p["instruction"], "No instructions for this package")) . "</instructions>";
        echo "<changelog>" . $p["changelog"] . "</changelog>";
        echo "<createdon>" . strftime("%Y-%m-%dT%H:%M:%SZ", $p["date"]) . "</createdon>";
        echo "<createdby>" . $this->ifnull($p["author"], "unknown") . "</createdby>";
        echo "<editedon>" . strftime("%Y-%m-%dT%H:%M:%SZ", $p["date"]) . "</editedon>";
        echo "<releasedon>" . strftime("%Y-%m-%dT%H:%M:%SZ", $p["date"]) . "</releasedon>";
        echo "<downloads>0</downloads>";
        echo "<approved>" . $this->ifnull($p["approved"], "true") . "</approved>";
        echo "<audited>" . $this->ifnull($p["audited"], "true") . "</audited>";
        echo "<featured>" . $this->ifnull($p["featured"], "true") . "</featured>";
        echo "<deprecated>" . $this->ifnull($p["deprecated"], "false") . "</deprecated>";
        echo "<license>" . $this->ifnull($p["license"], "GPLv2") . "</license>";
        echo "<smf_url/>";
        for ($j = 0; $j < count($this->repos); $j++) {
            $r = $this->repos[$j];
            if ($r["name"] == $p["repo"]) {
                echo "<repository>deadbeefc00000000000000$j</repository>";
            }
        }
        echo "<supports>" . $this->ifnull($p["modx_version"], "2.0") . "</supports>";
        if ($selected) {
            echo "<file>";
            echo "<id>deadbeefe00000000000000$i</id>";
            echo "<version>deadbeefe00000000000000$i</version>";
            echo "<filename>" . $p["signature"] . ".zip</filename>";
            echo "<downloads>1</downloads>";
            echo "<lastip>127.0.0.1</lastip>";
            echo "<transport>true</transport>";
            echo "<location>" . $p["location"] . "</location>";
            echo "</file>";
        } else {
            echo "<location>" . $p["location"] . "</location>";
        }
        echo "<signature>" . $p["signature"] . "</signature>";
        echo "<supports_db>" . $this->ifnull($p["modx_db"], "mysql") . "</supports_db>";
        echo "<minimum_supports>" . $this->ifnull($p["modx_version"], "2.0") . "</minimum_supports>";
        echo "<breaks_at>10000000.0</breaks_at>";
        echo "<screenshot>" . $this->ifnull($p["screenshot"], "") . "</screenshot>";
        echo "</package>";
    }

    /**
     * @param $packageName
     * @return mixed|string
     */
    function getLatestVersion($packageName)
    {
        $files = glob($this->options['packagePath'] . $packageName . '*.transport.zip');
        $lastSignature = '';
        foreach ($files as $file) {
            $signature = str_replace(array($this->options['packagePath'] . $packageName . '-', '.transport.zip'), '', $file);
            $lastSignature = (version_compare($signature, $lastSignature, '>')) ? $signature : $lastSignature;
        }
        return $lastSignature;
    }

    /**
     * @param $type
     * @param $msg
     */
    function log($type, $msg)
    {
        switch ($type) {
            case 'file':
                break;
            case 'log':
            default:
                error_log($msg);
                break;
        }
    }
}
$url = 'http://your.packageprovider.url/extras';
$debug = false;
$path = $_REQUEST['path'];

$pp = new PackageProvider();
$pp->repos[] = array(
    'name' => 'Main',
    'description' => 'All packages'
);

$existing_packages = array();
foreach (glob(dirname(__FILE__) . '/_packages/*.info.php') as $file) {
    $existing_packages[] = include_once($file);
}

/* Example for *.info.php
return = array(
    'repo' => 'Main', // The repositiory section for that package
    'name' => 'sample', // Lowercase package name
    'displayName' => 'Sample', // The name that is displayed in your MODX Repository
    'version' => $pp->getLatestVersion('sample'),
    'dir' => '_packages',
    'description' => 'Sample Package', // The description for that package
    'author' => 'Sample', // The Package author
    'modx_version' => '2.3', // Minimal MODX version for that package
    'users' => '' // Comma separated list of usernames that name could see/download the page
);
*/

$packages = $downloads = array();
foreach ($existing_packages as $package) {
    if (!$package['version']) {
        $package['version'] = '0.0.1-unknown';
    }
    $package['version_major'] = preg_replace('/([0-9]+)\\.([0-9]+)\\.([0-9]+)[-]?([a-zA-Z0-9\\.-]+)?/i', '$1', $package['version']);
    $package['version_minor'] = preg_replace('/([0-9]+)\\.([0-9]+)\\.([0-9]+)[-]?([a-zA-Z0-9\\.-]+)?/i', '$2', $package['version']);
    $package['version_patch'] = preg_replace('/([0-9]+)\\.([0-9]+)\\.([0-9]+)[-]?([a-zA-Z0-9\\.-]+)?/i', '$3', $package['version']);
    $package['version_release'] = preg_replace('/([0-9]+)\\.([0-9]+)\\.([0-9]+)[-]?([a-zA-Z0-9\\.-]+)?/i', '$4', $package['version']);
    $package['signature'] = strtolower($package['name'] . '-' . $package['version']);
    $package['location'] = (isset($package['url'])) ? $package['url'] : $url . '/download/' . $package['signature'] . '.zip';
    $package['url'] = $url . '/_packages/' . $package['signature'] . '.transport.zip';
    $package['date'] = filemtime($pp->options['packagePath'] . $package['signature'] . '.transport.zip');
    $downloads[] = $package;
    if ($_REQUEST['username'] && in_array($_REQUEST['username'], array_map('trim', explode(',', $package['users'])))) {
        $packages[] = $package;
    }
    if ($package['users'] == '') {
        $packages[] = $package;
    }
}

//die('<pre>' . print_r($packages, true));

switch ($path) {
    case 'verify':
        header("Content-Type: text/xml");
        if ($debug) {
            $pp->log('log', print_r($_REQUEST, true));
        }
        echo "<status><verified type=\"integer\">1</verified></status>";
        break;
    case 'welcome':
        header("Content-Type: text/xml");
        if ($debug) {
            $pp->log('log', print_r($_REQUEST, true));
        }
        echo "<home>";
        echo "<packages>" . count($packages) . "</packages>";
        echo "<downloads>" . count($packages) . "</downloads>";
        for ($i = 0; $i < count($packages); $i++) {
            $p = $packages[$i];
            echo "<topdownloaded>";
            echo "<id>deadbeefa00000000000000$i</id>";
            echo "<name>" . $pp->ifnull($p["displayName"], $p["name"]) . "</name>";
            echo "<downloads>1</downloads>";
            echo "</topdownloaded>";
        }
        for ($i = 0; $i < count($packages); $i++) {
            $p = $packages[$i];
            echo "<newest>";
            echo "<id>deadbeefa00000000000000$i</id>";
            echo "<name>" . $p["signature"] . "</name>";
            echo "<package_name>" . $pp->ifnull($p["displayName"], $p["name"]) . "</package_name>";
            echo "<releasedon>" . strftime("%Y-%m-%dT%H:%M:%SZ", $p["date"]) . "</releasedon>";
            echo "</newest>";
        }
        echo "<url>$url/package</url>";
        echo "</home>";
        break;

    case 'repository':
        header("Content-Type: text/xml");
        if ($debug) {
            $pp->log('log', print_r($_REQUEST, true));
        }
        $repo = $_REQUEST["repo"];
        if (!$repo) {
            echo "<repositories type=\"array\" of=\"" . count($pp->repos) . "\" total=\"" . count($pp->repos) . "\" page=\"1\">";
        }
        $found = false;
        for ($i = 0; $i < count($pp->repos); $i++) {
            $r = $pp->repos[$i];
            if (!$repo || "deadbeefc00000000000000$i" == $repo) {
                echo "<repository>";
                echo "<rank type=\"integer\">0</rank>";
                echo "<name>" . $r["name"] . "</name>";
                echo "<description>" . $pp->ifnull($r["description"], "No description for this repository") . "</description>";
                echo "<templated type=\"integer\">0</templated>";
                echo "<id>deadbeefc00000000000000$i</id>";
                $cc = 0;
                for ($j = 0; $j < count($packages); $j++) {
                    if ($packages[$j]["repo"] == $r["name"]) {
                        $cc++;
                    }
                }
                echo "<packages type=\"integer\">$cc</packages>";
                echo "<createdon type=\"datetime\">" . strftime("%Y-%m-%dT%H:%M:%SZ", $r["date"]) . "</createdon>";
                echo "<tag>";
                echo "<id>deadbeefd00000000000000$i</id>";
                echo "<name>All</name>";
                echo "<packages>$cc</packages>";
                echo "<templated>0</templated>";
                echo "</tag>";
                echo "</repository>";
                $found = true;
            }
        }
        if (!$repo) {
            echo "</repositories>";
        }
        if ($repo && !$found) {
            echo "<error><message>No repository found</message></error>";
        }
        break;

    case 'package':
        header("Content-Type: text/xml");
        if ($debug) {
            $pp->log('log', print_r($_REQUEST, true));
        }
        $sig = $_REQUEST["signature"];
        if (!$sig) {
            echo "<packages of=\"" . count($packages) . "\" total=\"" . count($packages) . "\" page=\"1\">";
        }
        $found = false;
        for ($i = 0; $i < count($packages); $i++) {
            $p = $packages[$i];
            if (!$sig || $sig == $p["signature"]) {
                $pp->write_package($i, $p, $sig == $p["signature"]);
                $found = true;
            }
        }
        if (!$sig) {
            echo "</packages>";
        }
        if ($sig && !$found) {
            echo "<error><message>No package found</message></error>";
        }

        break;

    case 'update':
        header("Content-Type: text/xml");
        if ($debug) {
            $pp->log('log', print_r($_REQUEST, true));
        }
        $sig = $_REQUEST["signature"];
        $op = array();
        $op["name"] = preg_replace("/([a-zA-Z0-9_]+)-([0-9]+)\\.([0-9]+)\\.([0-9]+)[-]?([a-zA-Z0-9\\.-]+)?/i", "\${1}", $sig);
        $op["version_major"] = preg_replace("/([a-zA-Z0-9_]+)-([0-9]+)\\.([0-9]+)\\.([0-9]+)[-]?([a-zA-Z0-9\\.-]+)?/i", "\${2}", $sig);
        $op["version_minor"] = preg_replace("/([a-zA-Z0-9_]+)-([0-9]+)\\.([0-9]+)\\.([0-9]+)[-]?([a-zA-Z0-9\\.-]+)?/i", "\${3}", $sig);
        $op["version_patch"] = preg_replace("/([a-zA-Z0-9_]+)-([0-9]+)\\.([0-9]+)\\.([0-9]+)[-]?([a-zA-Z0-9\\.-]+)?/i", "\${4}", $sig);
        $relfound = 0;
        $op["version_release"] = preg_replace("/([a-zA-Z0-9_]+)-([0-9]+)\\.([0-9]+)\\.([0-9]+)[-]?([a-zA-Z0-9\\.-]+)?/i", "\${5}", $sig, -1, &$relfound);
        if (!$relfound) {
            $op["version_release"] = "";
        }
        $found = false;
        for ($i = 0; $i < count($packages); $i++) {
            $p = $packages[$i];
            if (strtolower($p["name"]) == $op["name"] && $p["signature"] != $sig) {
                $found = true;
                echo "<packages of=\"1\" total=\"1\" page=\"1\">";
                $pp->write_package($i, $p, false);
                echo "</packages>";
                break;
            }
        }

        if (!$found) {
            echo "<packages of=\"0\" total=\"0\" page=\"0\">";
            echo "</packages>";
        }
        break;

    case 'download':
        if ($debug) {
            $pp->log('log', print_r($_REQUEST, true));
        }
        $sig = $_REQUEST["signature"];
        $debug = $_REQUEST["debug"];
        $getUrl = $_REQUEST["getUrl"];    // Request is only for filename
        if ($getUrl) {
            echo "$url/download/$sig";
            return;
        }
        if (!$sig) {
            echo "<error><message>No package specified</message></error>";
        } else {
            $found = false;
            if (strstr($sig, ".zip") == ".zip") {
                $sig = substr($sig, 0, strlen($sig) - strlen(".zip"));
            }
            for ($i = 0; $i < count($downloads); $i++) {
                $p = $downloads[$i];
                if ($sig == $p["signature"]) {
                    if ($p["url"]) {
                        // Forward to URL
                        header("Location: " . $p["url"]);
                    } else {
                        header("Content-Type: text/xml");
                        echo "<error><message>No way to read package ZIP</message></error>";
                    }
                    $found = true;
                }
            }
            if (!$found) {
                echo "<error><message>No package $sig found</message></error>";
            }
        }
        break;

    default:
        echo <<<HTML
<h1>Basic package repository</h1>
<p>This is a onepage MODX package repository implementation.</p>
<h2>Relevant MODX REST calls</h2>
<ul>
    <li><a href="verify">verify</a> (called on adding repo)</li>
    <li><a href="home">home</a> (display repo welcome page)</li>
    <li><a href="repository">repository</a> (read repository tree and tags)</li>
    <li><a href="package">package</a> (read packages in repo or by tag)</li>
    <li><a href="package?signature={$packages[0]['signature']}">package?signature=</a> (specific package)</li>
    <li><a href="package/update?signature={$packages[0]['signature']}">package/update</a> (check updates)</li>
</ul>
HTML;
        break;
}
?>
