<?php
include('config.php');
// include i18n class and initialize it
require_once 'i18n.class.php';
$i18n = new i18n();
$i18n->setCachePath('cache');
$i18n->setFilePath('lang/{LANGUAGE}.ini');
$i18n->setFallbackLang('en');
$i18n->init();
// Check whether the php-exif library is installed
if (!extension_loaded('exif')) {
    exit('<center><code style="color: red;">' . L::warning_php_exif . '</code></center>');
}

class DiskSpaceCheck
{
    public $total_space = false;
    public $free_space = false;
    public $used_space = false;
    public $percent = false;

    function __construct($directory = null)
    {
        if ($directory === null) {
            $directory = dirname(__FILE__);
        }
        $this->total_space    = disk_total_space($directory);
        $this->free_space     = disk_free_space($directory);
        $this->used_space     = $this->total_space - $this->free_space;
        $this->percent        = (($this->used_space / $this->total_space) * 100);
    }

    function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

function round_to_ten($value)
{
    return round($value / 10) * 10;
}
?>

<!DOCTYPE html>
<html lang="<?php echo $i18n->getAppliedLang(); ?>">

<!--
	 Author: Dmitri Popov
	 License: GPLv3 https://www.gnu.org/licenses/gpl-3.0.txt
	 Source code: https://github.com/dmpop/pellicola
	-->

<head>
    <meta charset="utf-8">
    <link rel="shortcut icon" href="favicon.png" />
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="styles.css" />

    <title><?php echo $TITLE; ?></title>
</head>

<body>
    <div id="content">
        <div style="text-align:center; margin-bottom: 1.5em; margin-top: 1.5em;">
            <a style="text-decoration:none;" href="index.php"><img style="display: inline; height: 3.5em; vertical-align: middle;" src="favicon.png" alt="<?php echo $TITLE; ?>" /></a>
            <a style="text-decoration:none;" href="index.php">
                <h1 style="display: inline; font-size: 2.3em; margin-left: 0.19em; vertical-align: middle; letter-spacing: 3px; color: #59a2d8ff;"><?php echo $TITLE ?></h1>
            </a>
        </div>
        <div class="center" style="color: gray; margin-bottom: 1em;"><?php echo $SUBTITLE ?></div>
        <hr>
        <?php
        function rsearch($dir, $excluded, $pattern_array)
        {
            $return = array();
            $iti = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
            foreach (new RecursiveIteratorIterator($iti) as $file => $details) {
                if (!is_file($iti->getBasename()) && ($iti->getBasename() != $excluded)) {
                    $file_ext = pathinfo($file, PATHINFO_EXTENSION);
                    if (in_array($file_ext, $pattern_array)) {
                        $return[] = $file;
                    }
                }
            }
            return $return;
        }
        ?>
        <div class='c' style='width: 30em;'>
            <details open>
                <summary><?php echo L::stats; ?></summary>
                <table>
                    <tr>
                        <td><?php echo L::total_views; ?></td>
                        <td>
                            <?php
                            $views_count = 0;
                            // Get all .views files
                            $all_views_files = glob($STATS_DIR . DIRECTORY_SEPARATOR . "*.views");
                            // Read value from each .views file and add it $views_count
                            foreach ($all_views_files  as $views_file) {
                                $views_count += fgets(fopen($views_file, 'r'));
                            }
                            echo '<strong>' . $views_count . '</strong>';
                            ?>
                        </td>
                    </tr>

                    <?php if ($DOWNLOAD) : ?>
                        <tr>
                            <td><?php echo L::total_downloads; ?></td>
                            <td>
                                <?php
                                $downloads_count = 0;
                                // Get all .downloads files
                                $all_downloads_files = glob($STATS_DIR . DIRECTORY_SEPARATOR . "*.downloads");
                                // Read value from each .download file and add it $downloads_count
                                foreach ($all_downloads_files  as $downloads_file) {
                                    $downloads_count += fgets(fopen($downloads_file, 'r'));
                                }
                                echo '<strong>' . $downloads_count . '<strong>';
                                ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </table>
            </details>

            <details>
                <summary><?php echo L::storage; ?></summary>
                <?php
                $disk = new DiskSpaceCheck(dirname(__FILE__));
                ?>
                <table>
                    <tr>
                        <td><?php echo L::total_storage; ?></td>
                        <td><strong><?php echo $disk->formatBytes($disk->total_space); ?></strong></td>
                    </tr>
                    <tr>
                        <td><?php echo L::used_storage; ?></td>
                        <td><strong><?php echo $disk->formatBytes($disk->used_space); ?></strong> (<?php echo floor($disk->percent); ?>%) <progress value="<?php echo $disk->percent; ?>" max="100"><?php echo $disk->percent; ?></progress></td>
                    </tr>
                </table>
            </details>

            <?php if (`which vnstati`) : ?>
                <details>
                    <summary><?php echo L::network_traffic; ?></summary>
                    <?php
                    shell_exec("vnstati -m -s -o vnstat.png");
                    ?>
                    <img style="max-width:100%; height: auto; margin-top: 1em;" src="vnstat.png" />
                </details>
            <?php endif; ?>

            <?php
            $files = rsearch($ROOT_PHOTO_DIR, 'tims', explode(',', $IMG_FORMATS));

            $model = array();
            foreach ($files as $file) {
                $exif = @exif_read_data($file);
                if (!empty($exif["Model"])) {
                    array_push($model, $exif["Model"]);
                }
            }
            $f_length = array();
            foreach ($files as $file) {
                $exif = @exif_read_data($file);
                if (!empty($exif['FocalLength'])) {
                    // Focal length in $exif['FocalLength'] is stored as X/10
                    // The eval() function perform the division to calculate the actual focal length in mm
                    $f_length_mm = eval('return ' . $exif['FocalLength'] . ';');
                    // The round_to_ten() function rounds the focal length value up or down to the nearest 10
                    $f_length_rounded = (int)round_to_ten($f_length_mm);
                    array_push($f_length, $f_length_rounded);
                }
            }
            echo '
            <details>
                  <summary>' . L::camera_model . '</summary>
                    <table>
        ';
            $count = array_count_values(array_filter($model));
            arsort($count);
            foreach ($count as $key => $value) {
                echo "<tr><td>$key</td><td style='text-align: right;'>$value</td></tr>";
            }
            echo '
        </table>
        </details>
        ';

            echo '
        <details>
                  <summary>' . L::f_length  . '</summary>
                    <table>
        ';
            $count = array_count_values(array_filter($f_length));
            arsort($count);
            foreach ($count as $key => $value) {
                echo "<tr><td>$key</td><td>$value</td></tr>";
            }
            echo '
        </table>
        </details>
        ';
            ?>
        </div>
        <div class="center" style="margin-top: 1em; margin-bottom: 3.5em;">
            <a class="btn primary" style="text-decoration: none;" href="index.php"><?php echo L::btn_back; ?></a>
        </div>
        <?php
        // Show links and footer
        if ($LINKS) {
            $array_length = count($URLS);
            echo '<div class="footer">';
            for ($i = 0; $i < $array_length; $i++) {
                echo '<span style="word-spacing:0.1em;"><a style="color: white" href="' . $URLS[$i][0] . '">' . $URLS[$i][1] . '</a> • </span>';
            }
            echo  $FOOTER . '</div>';
        } else {
            echo '<div class="footer"' . $FOOTER . '</div>';
        }
        ?>
</body>

</html>