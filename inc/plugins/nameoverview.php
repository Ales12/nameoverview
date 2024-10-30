<?php

// Disallow direct access to this file for security reasons
if (!defined("IN_MYBB")) {
    die("Direct initialization of this file is not allowed.");
}

// Hooks
// ADMIN-CP PEEKER
$plugins->add_hook('admin_config_settings_change', 'nameoverview_settings_change');
$plugins->add_hook('admin_settings_print_peekers', 'nameoverview_settings_peek');
// Misc
$plugins->add_hook('misc_start', 'nameoverview_misc');
//wer ist wo
$plugins->add_hook('fetch_wol_activity_end', 'nameoverview_user_activity');
$plugins->add_hook('build_friendly_wol_location_end', 'nameoverview_location_activity');

function nameoverview_info()
{
    return array(
        "name" => "Übersicht der vergebenen Namen",
        "description" => "Hier findest du eine Übersicht aller Namen, die im Forum vertreten sind.",
        "website" => "https://github.com/Ales12/nameoverview",
        "author" => "Ales",
        "authorsite" => "https://github.com/Ales12",
        "version" => "1.0",
        "guid" => "",
        "codename" => "",
        "compatibility" => "*"
    );
}

function nameoverview_install()
{
    global $db, $mybb;

    $setting_group = array(
        'name' => 'nameoverview',
        'title' => 'Einstellungen für die Namensübersicht',
        'description' => 'Hier kannst du alles für die Namensübersicht einstellen',
        'disporder' => 5, // The order your setting group will display
        'isdefault' => 0
    );

    $gid = $db->insert_query("settinggroups", $setting_group);
    $setting_array = array(
        // A text setting
        'name_gender' => array(
            'title' => 'Profilfeld für die Geschlechter',
            'description' => 'Gebe hier die FID des Profils an, in welchen das Geschlecht ausgewählt wird:',
            'optionscode' => 'numeric',
            'value' => '3', // Default
            'disporder' => 1
        ),
        // A yes/no boolean box
        'name_divers' => array(
            'title' => 'Auch Divers anzeigen?',
            'description' => 'Soll neben Männlich und Weiblich auch Divers aufgelistet werden?',
            'optionscode' => 'yesno',
            'value' => 1,
            'disporder' => 2
        ),
        'name_desc' => array(
            'title' => 'Beschreibungstext',
            'description' => 'Hier kannst du einen Beschreibungstext einfügen. Zum Beispiel, wie oft ein Vorname vergeben werden darf oder wie die Regelung bei Nachnamen ist. Möchtest du nichts einfügen, dann lass es einfach leer.',
            'optionscode' => 'textarea',
            'value' => "",
            'disporder' => 3
        ),
        'name_surenameyesno' => array(
            'title' => 'Nachname über Profilfeld auslesen',
            'description' => 'Möchtest du den Nachnamen über ein Profilfeld auslesen lassen? Dies kann helfen, wenn es Charaktere mit zwei Vornamen existieren.',
            'optionscode' => 'yesno',
            'value' => 0,
            'disporder' => 4
        ),
        'name_surnamepf' => array(
            'title' => 'Profilfeld für Nachname',
            'description' => 'Gebe hier die FID des Profilfeldes an, in welchen der Nachname angegeben wird.',
            'optionscode' => 'numeric',
            'value' => "1",
            'disporder' => 5
        ),
        'name_noshow' => array(
            'title' => 'Ausgeschlossene Account',
            'description' => 'Gebe hier an, welche Accounts nicht angezeigt werden sollen. Wenn du es nicht brauchst, lass es einfach so.',
            'optionscode' => 'text ',
            'value' => "-99,-98",
            'disporder' => 6
        ),
    );

    foreach ($setting_array as $name => $setting) {
        $setting['name'] = $name;
        $setting['gid'] = $gid;

        $db->insert_query('settings', $setting);
    }


    $insert_array = array(
        'title' => 'nameoverview',
        'template' => $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->nameoverview}</title>
{$headerinclude}
</head>
<body>
{$header}

<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead"><strong>{$lang->nameoverview}</strong></td>
</tr>
<tr>
<td class="trow1" valign="top">
	<div class="name_desc">{$rulesdesc}</div>
<div class="name_flexbox">
{$name_namelist}
	</div>
</td>
</tr>
</table>
{$footer}
</body>
</html>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'nameoverview_names',
        'template' => $db->escape_string('<div class="name">{$name}</div>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'nameoverview_overview',
        'template' => $db->escape_string('	<div class="name_top tcat">{$lang->nameoverview_m}</div>
		<div class="name_top tcat">{$lang->nameoverview_f}</div>
		<div class="name_top tcat">{$lang->nameoverview_surname}</div>
<div class="name_alphabet trow_sep">{$lang->nameoverview_abcd}</div>
<div class="name_list trow1">
{$names_abcd_m}	
</div>
<div class="name_list trow1">
{$names_abcd_f}	
</div>
<div class="name_list trow1">
{$names_abcd_n}	
</div>
<div class="name_alphabet trow_sep">{$lang->nameoverview_efgh}</div>
<div class="name_list trow1">
{$names_efgh_m}	
</div>
<div class="name_list trow1">
{$names_efgh_f}	
</div>
<div class="name_list trow1">
{$names_efgh_n}	
</div>
<div class="name_alphabet trow_sep">{$lang->nameoverview_ijkl}</div>
<div class="name_list trow1">
{$names_ijkl_m}	
</div>
<div class="name_list trow1">
{$names_ijkl_f}	
</div>
<div class="name_list trow1">
{$names_ijkl_n}	
</div>
<div class="name_alphabet trow_sep">{$lang->nameoverview_mnop}</div>
<div class="name_list trow1">
{$names_mnop_m}	
</div>
<div class="name_list trow1">
{$names_mnop_f}	
</div>
<div class="name_list trow1">
{$names_mnop_n}	
</div>
<div class="name_alphabet trow_sep">{$lang->nameoverview_qrst}</div>
<div class="name_list trow1">
{$names_qrst_m}	
</div>
<div class="name_list trow1">
{$names_qrst_f}	
</div>
<div class="name_list trow1">
{$names_qrst_n}	
</div>
<div class="name_alphabet trow_sep">{$lang->nameoverview_uvwxyz}</div>
<div class="name_list trow1">
{$names_uvqxyz_m}	
</div>
<div class="name_list trow1">
{$names_uvqxyz_f}	
</div>
<div class="name_list trow1">
{$names_uvqxyz_n}	
</div>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'nameoverview_overview_divers',
        'template' => $db->escape_string('	<div class="name_top_divers tcat">{$lang->nameoverview_m}</div>
		<div class="name_top_divers tcat">{$lang->nameoverview_f}</div>
		<div class="name_top_divers tcat">{$lang->nameoverview_d}</div>
		<div class="name_top_divers tcat">{$lang->nameoverview_surname}</div>
<div class="name_alphabet trow_sep">{$lang->nameoverview_abcd}</div>
<div class="name_list_divers trow1">
{$names_abcd_m}	
</div>
<div class="name_list_divers trow1">
{$names_abcd_f}	
</div>
<div class="name_list_divers trow1">
{$names_abcd_d}	
</div>
<div class="name_list_divers trow1">
{$names_abcd_n}	
</div>
<div class="name_alphabet trow_sep">{$lang->nameoverview_efgh}</div>
<div class="name_list_divers trow1">
{$names_efgh_m}	
</div>
<div class="name_list_divers trow1">
{$names_efgh_f}	
</div>
<div class="name_list_divers trow1">
{$names_efgh_d}	
</div>
<div class="name_list_divers trow1">
{$names_efgh_n}	
</div>
<div class="name_alphabet trow_sep">{$lang->nameoverview_ijkl}</div>
<div class="name_list_divers trow1">
{$names_ijkl_m}	
</div>
<div class="name_list_divers trow1">
{$names_ijkl_f}	
</div>
<div class="name_list_divers trow1">
{$names_ijkl_d}	
</div>
<div class="name_list_divers trow1">
{$names_ijkl_n}	
</div>
<div class="name_alphabet trow_sep">{$lang->nameoverview_mnop}</div>
<div class="name_list_divers trow1">
{$names_mnop_m}	
</div>
<div class="name_list_divers trow1">
{$names_mnop_f}	
</div>
<div class="name_list_divers trow1">
{$names_mnop_d}	
</div>
<div class="name_list_divers trow1">
{$names_mnop_n}	
</div>
<div class="name_alphabet trow_sep">{$lang->nameoverview_qrst}</div>
<div class="name_list_divers trow1">
{$names_qrst_m}	
</div>
<div class="name_list_divers trow1">
{$names_qrst_f}	
</div>
<div class="name_list_divers trow1">
{$names_qrst_d}	
</div>
<div class="name_list_divers trow1">
{$names_qrst_n}	
</div>
<div class="name_alphabet trow_sep">{$lang->nameoverview_uvwxyz}</div>
<div class="name_list_divers trow1">
{$names_uvqxyz_m}	
</div>
<div class="name_list_divers trow1">
{$names_uvqxyz_f}	
</div>
<div class="name_list_divers trow1">
{$names_uvqxyz_d}	
</div>
<div class="name_list_divers trow1">
{$names_uvqxyz_n}	
</div>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'nameoverview_surnames',
        'template' => $db->escape_string('<div class="name">
	{$surname} {$nameowner}
</div>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    //CSS einfügen
    $css = array(
        'name' => 'nameoverview.css',
        'tid' => 1,
        'attachedto' => '',
        "stylesheet" => '.name_desc{
	margin: 10px 20px;
	text-align: justify;
}
        
        .name_flexbox{
	display: flex;
	justify-content: space-evenly;
	 flex-flow: row wrap;
	gap: 10px 5px;
}

.name_top_divers{
	width: 24.5%;
	padding: 5px 10px;
	box-sizing: border-box;
	text-align: center;
}

.name_top{
	width: 33%;
	padding: 5px 10px;
	box-sizing: border-box;
	text-align: center;
}

.name_list{
	padding: 5px 10px;
	box-sizing: border-box;
	width: 24.5%;
}

.name_list{
	padding: 5px 10px;
	box-sizing: border-box;
		width: 33%;
}


.name_list > .name{
	margin: 2px auto;
}

.name_list > .name:before{
	content: "» ";
	padding-right: 5px;
}

.name_list > .name span{
	font-weight: bold;	
}

.name_list_divers{
	padding: 5px 10px;
	box-sizing: border-box;
	width: 24.5%;
}

.name_list_divers > .name{
	margin: 2px auto;
}

.name_list_divers > .name:before{
	content: "» ";
	padding-right: 5px;
}

.name_list_divers > .name span{
	font-weight: bold;	
}

.name_alphabet{
	font-weight: bold;
	width: 100%;
}  ',
        'cachefile' => $db->escape_string(str_replace('/', '', 'nameoverview.css')),
        'lastmodified' => time()
    );


    require_once MYBB_ADMIN_DIR . "inc/functions_themes.php";

    $sid = $db->insert_query("themestylesheets", $css);
    $db->update_query("themestylesheets", array("cachefile" => "css.php?stylesheet=" . $sid), "sid = '" . $sid . "'", 1);

    $tids = $db->simple_select("themes", "tid");
    while ($theme = $db->fetch_array($tids)) {
        update_theme_stylesheet_list($theme['tid']);
    }

    // Don't forget this!
    rebuild_settings();

}

function nameoverview_is_installed()
{

    global $mybb;
    if (isset($mybb->settings['name_gender'])) {
        return true;
    }

    return false;
}

function nameoverview_uninstall()
{
    global $db;

    $db->delete_query('settings', "name IN ('name_gender','name_divers','name_desc',  'name_surenameyesno', 'name_surnamepf', 'name_noshow')");
    $db->delete_query('settinggroups', "name = 'nameoverview'");

    $db->delete_query("templates", "title LIKE '%nameoverview%'");

    require_once MYBB_ADMIN_DIR . "inc/functions_themes.php";
    $db->delete_query("themestylesheets", "name = 'nameoverview.css'");
    $query = $db->simple_select("themes", "tid");
    while ($theme = $db->fetch_array($query)) {
        update_theme_stylesheet_list($theme['tid']);
    }
    // Don't forget this
    rebuild_settings();

}

function nameoverview_activate()
{

}

function nameoverview_deactivate()
{

}
function nameoverview_settings_change()
{
    global $db, $mybb, $nameoverview_settings_peeker;

    $result = $db->simple_select('settinggroups', 'gid', "name='nameoverview'", array("limit" => 1));
    $group = $db->fetch_array($result);
    $nameoverview_settings_peeker = ($mybb->input['gid'] == $group['gid']) && ($mybb->request_method != 'post');
}
function nameoverview_settings_peek(&$peekers)
{
    global $mybb, $nameoverview_settings_peeker;

    if ($nameoverview_settings_peeker) {
        $peekers[] = 'new Peeker($(".setting_name_surenameyesno"), $("#row_setting_name_surnamepf"),/1/,true)';

    }
}




// In the body of your plugin
function nameoverview_misc()
{
    global $mybb, $templates, $lang, $header, $headerinclude, $footer, $lang, $db, $parser;
    $lang->load('nameoverview');

    require_once MYBB_ROOT . "inc/class_parser.php";
    $parser = new postParser;

    $options = array(
        "allow_html" => 1,
        "allow_mycode" => 1,
        "allow_smilies" => 1,
        "allow_imgcode" => 1,
        "filter_badwords" => 0,
        "nl2br" => 1,
        "allow_videocode" => 0
    );

    $overviewtyp = $mybb->settings['name_divers'];
    $genderfid = "fid" . $mybb->settings['name_gender'];
    $desc = $mybb->settings['name_desc'];
    $surenameowner = $mybb->settings['name_surenameowner'];
    $surname_pf = "fid" . $mybb->settings['name_surnamepf'];
    $surnameyn_pf = $mybb->settings['name_surenameyesno'];
    $noshow = $mybb->settings['name_noshow'];

    // Einstellungen

    if ($mybb->get_input('action') == 'nameoverview') {
        // Do something, for example I'll create a page using the hello_world_template

        // Add a breadcrumb
        add_breadcrumb($lang->nameoverview, "misc.php?action=nameoverview");

        $surname_array = array();
        $exclude = "";
        if ($noshow != "-99,-98") {
            $exclude = "and u.uid NOT IN ('" . str_replace(',', '\',\'', $noshow) . "')";
        }

        $get_charanames = $db->query("SELECT *
        FROM " . TABLE_PREFIX . "users u
        LEFT JOIN " . TABLE_PREFIX . "userfields uf
        on (u.uid = uf.ufid)
        where {$genderfid} != ''
        {$exclude}
        ORDER BY username ASC
        ");

        while ($names = $db->fetch_array($get_charanames)) {
            $name = "";
            $gender = "";
            $nameowner = "";
            $surname = "";
            $surnames = "";
            $rulesdesc = "";

            $get_name = explode(" ", $names['username']);
            $name = $get_name[0];
            $gender = $names[$genderfid];
            $rulesdesc = $parser->parse_message($desc, $options);

            // abcd
            if (preg_match("/^(A|a|B|b|C|c|D|d)/", $name) && ($gender == 'weiblich' || $gender == 'female')) {
                eval ("\$names_abcd_f .= \"" . $templates->get("nameoverview_names") . "\";");
            } elseif (preg_match("/^(A|a|B|b|C|c|D|d)/", $name) && ($gender == 'männlich' || $gender == 'male')) {
                eval ("\$names_abcd_m .= \"" . $templates->get("nameoverview_names") . "\";");
            } elseif (preg_match("/^(A|a|B|b|C|c|D|d)/", $name) && $gender == 'divers') {
                eval ("\$names_abcd_d .= \"" . $templates->get("nameoverview_names") . "\";");
            } // efgh
            elseif (preg_match("/^(E|e|F|f|G|g|H|h)/", $name) && ($gender == 'weiblich' || $gender == 'female')) {
                eval ("\$names_efgh_f .= \"" . $templates->get("nameoverview_names") . "\";");
            } elseif (preg_match("/^(E|e|F|f|G|g|H|h)/", $name) && ($gender == 'männlich' || $gender == 'male')) {
                eval ("\$names_efgh_m .= \"" . $templates->get("nameoverview_names") . "\";");
            } elseif (preg_match("/^(E|e|F|f|G|g|H|h)/", $name) && $gender == 'divers') {
                eval ("\$names_efgh_d .= \"" . $templates->get("nameoverview_names") . "\";");
            } // ijkl
            elseif (preg_match("/^(I|i|J|j|K|k|L|l)/", $name) && ($gender == 'weiblich' || $gender == 'female')) {
                eval ("\$names_ijkl_f .= \"" . $templates->get("nameoverview_names") . "\";");
            } elseif (preg_match("/^(I|i|J|j|K|k|L|l)/", $name) && ($gender == 'männlich' || $gender == 'male')) {
                eval ("\$names_ijkl_m .= \"" . $templates->get("nameoverview_names") . "\";");
            } elseif (preg_match("/^(I|i|J|j|K|k|L|l)/", $name) && $gender == 'divers') {
                eval ("\$names_ijkl_d .= \"" . $templates->get("nameoverview_names") . "\";");
            } // mnop
            elseif (preg_match("/^(M|m|N|n|O|o|P|p)/", $name) && ($gender == 'weiblich' || $gender == 'female')) {
                eval ("\$names_mnop_f .= \"" . $templates->get("nameoverview_names") . "\";");
            } elseif (preg_match("/^(M|m|N|n|O|o|P|p)/", $name) && ($gender == 'männlich' || $gender == 'male')) {
                eval ("\$names_mnop_m .= \"" . $templates->get("nameoverview_names") . "\";");
            } elseif (preg_match("/^(M|m|N|n|O|o|P|p)/", $name) && $gender == 'divers') {
                eval ("\$names_mnop_d .= \"" . $templates->get("nameoverview_names") . "\";");
            }// qrst
            elseif (preg_match("/^(Q|q|R|r|S|s|T|t)/", $name) && ($gender == 'weiblich' || $gender == 'female')) {
                eval ("\$names_qrst_f .= \"" . $templates->get("nameoverview_names") . "\";");
            } elseif (preg_match("/^(Q|q|R|r|S|s|T|t)/", $name) && ($gender == 'männlich' || $gender == 'male')) {
                eval ("\$names_qrst_m .= \"" . $templates->get("nameoverview_names") . "\";");
            } elseif (preg_match("/^(Q|q|R|r|S|s|T|t)/", $name) && $gender == 'divers') {
                eval ("\$names_qrst_d .= \"" . $templates->get("nameoverview_names") . "\";");
            } // uvwxyz
            elseif (preg_match("/^(U|u|V|v|W|w|X|x|Y|y|Z|z)/", $name) && ($gender == 'weiblich' || $gender == 'female')) {
                eval ("\$names_uvwxyz_f .= \"" . $templates->get("nameoverview_names") . "\";");
            } elseif (preg_match("/^(U|u|V|v|W|w|X|x|Y|y|Z|z)/", $name) && ($gender == 'männlich' || $gender == 'male')) {
                eval ("\$names_uvwxyz_m .= \"" . $templates->get("nameoverview_names") . "\";");
            } elseif (preg_match("/^(U|u|V|v|W|w|X|x|Y|y|Z|z)/", $name) && $gender == 'divers') {
                eval ("\$names_uvwxyz_d .= \"" . $templates->get("nameoverview_names") . "\";");
            }

            $count = count($get_name);

            if ($count == 2) {
                $surname = $get_name[1];
                array_push($surname_array, $surname);
            }

        }

        if ($surnameyn_pf == 0) {
            $surname_array = array_unique($surname_array);
            asort($surname_array);
            foreach ($surname_array as $surname) {
                // abcd
                if (preg_match("/^(A|a|B|b|C|c|D|d)/", $surname)) {
                    eval ("\$names_abcd_n .= \"" . $templates->get("nameoverview_surnames") . "\";");
                } // efgh
                elseif (preg_match("/^(E|e|F|f|G|g|H|h)/", $surname)) {
                    eval ("\$names_efgh_n .= \"" . $templates->get("nameoverview_surnames") . "\";");
                } // ijkl
                elseif (preg_match("/^(I|i|J|j|K|k|L|l)/", $surname)) {
                    eval ("\$names_ijkl_n .= \"" . $templates->get("nameoverview_surnames") . "\";");
                } // mnop
                elseif (preg_match("/^(M|m|N|n|O|o|P|p)/", $surname)) {
                    eval ("\$names_mnop_n .= \"" . $templates->get("nameoverview_surnames") . "\";");
                }// qrst
                elseif (preg_match("/^(Q|q|R|r|S|s|T|t)/", $surname)) {
                    eval ("\$names_qrst_n .= \"" . $templates->get("nameoverview_surnames") . "\";");
                } // uvwxyz
                elseif (preg_match("/^(U|u|V|v|W|w|X|x|Y|y|Z|z)/", $surname)) {
                    eval ("\$names_uvwxyz_n .= \"" . $templates->get("nameoverview_surnames") . "\";");
                }
            }


        } else {
            $get_pf = $db->query("SELECT DISTINCT {$surname_pf}
                FROM " . TABLE_PREFIX . "userfields
                WHERE {$surnameyn_pf} != ''
                ORDER BY {$surname_pf} ASC
                ");

            while ($row = $db->fetch_array($get_pf)) {
                $surname = $row[$surname_pf];
                // abcd
                if (preg_match("/^(A|a|B|b|C|c|D|d)/", $surname)) {
                    eval ("\$names_abcd_n .= \"" . $templates->get("nameoverview_surnames") . "\";");
                } // efgh
                elseif (preg_match("/^(E|e|F|f|G|g|H|h)/", $surname)) {
                    eval ("\$names_efgh_n .= \"" . $templates->get("nameoverview_surnames") . "\";");
                } // ijkl
                elseif (preg_match("/^(I|i|J|j|K|k|L|l)/", $surname)) {
                    eval ("\$names_ijkl_n .= \"" . $templates->get("nameoverview_surnames") . "\";");
                } // mnop
                elseif (preg_match("/^(M|m|N|n|O|o|P|p)/", $surname)) {
                    eval ("\$names_mnop_n .= \"" . $templates->get("nameoverview_surnames") . "\";");
                }// qrst
                elseif (preg_match("/^(Q|q|R|r|S|s|T|t)/", $surname)) {
                    eval ("\$names_qrst_n .= \"" . $templates->get("nameoverview_surnames") . "\";");
                } // uvwxyz
                elseif (preg_match("/^(U|u|V|v|W|w|X|x|Y|y|Z|z)/", $surname)) {
                    eval ("\$names_uvwxyz_n .= \"" . $templates->get("nameoverview_surnames") . "\";");
                }
            }
        }

        if ($overviewtyp == 1) {
            eval ("\$name_namelist = \"" . $templates->get("nameoverview_overview_divers") . "\";");
        } else {
            eval ("\$name_namelist = \"" . $templates->get("nameoverview_overview") . "\";");
        }

        // Using the misc_help template for the page wrapper
        eval ("\$page = \"" . $templates->get("nameoverview") . "\";");
        output_page($page);
    }
}

function nameoverview_user_activity($user_activity)
{
    global $user;
    if (my_strpos($user['location'], "misc.php?action=nameoverview") !== false) {
        $user_activity['activity'] = "nameoverview";
    }

    return $user_activity;
}

function nameoverview_location_activity($plugin_array)
{
    global $db, $mybb, $lang;
    $lang->load('nameoverview');
    if ($plugin_array['user_activity']['activity'] == "nameoverview") {
        $plugin_array['location_name'] = $lang->nameoverview_wiw;
    }
    return $plugin_array;
}