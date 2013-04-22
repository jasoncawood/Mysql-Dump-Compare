<?php
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>
<?php
	require_once('compare.class.php');

	if(isset($_REQUEST['Submit']))
	{

	}

    function GetLastFileName()
    {
        $FileName = '0';
         $default_dir = "./";
        // lists files only for the directory which this script is run from

        if(!($dp = opendir($default_dir))) die("Cannot open $default_dir.");

        while($file = readdir($dp))
        {
            if(is_dir($file))
            {
                continue;
            }
            else if($file != '.' && $file != '..')
            {
               $FileNameArr = explode(".",$file);
                if($FileNameArr[1] == 'sql')
                {
                    if($FileName == '')
                    {
                        $FileName = $FileNameArr[0];
                    }
                    else
                    {
                       if($FileName < $FileNameArr[0])
                       {
                           $FileName = $FileNameArr[0];
                       }
                    }

                }

            }
        }
        closedir($dp);

        return $FileName+1;
    }

?>
<body>

<?php
    if(isset($_REQUEST['step']) && $_REQUEST['step'] == 1)
    {

        include 'mysql_backup.class.php';

        $db_host = "localhost";    //---- Database host(usually localhost).
        $db_name = "flavio";    //---- Your database name.
        $db_user = "root";    //---- Your database username.
        $db_pass = "";    //---- Your database password.

        $output = "firstbackup.sql";
        $structure_only = false;

        $backup = new mysql_backup ($db_host,$db_name,$db_user,$db_pass,$output,$structure_only);

        $backup->backup();


    }
    else if($_REQUEST['step'] == '2')
    {

        include 'mysql_backup.class.php';

        $db_host = "localhost";    //---- Database host(usually localhost).
        $db_name = "flavio";    //---- Your database name.
        $db_user = "root";    //---- Your database username.
        $db_pass = "";    //---- Your database password.

        $output = 'secondbackup.sql';
        $structure_only = false;

        $backup = new mysql_backup ($db_host,$db_name,$db_user,$db_pass,$output,$structure_only);

        $backup->backup();

    }
    else if($_REQUEST['step'] == 'final')
    {

            echo'inside';
                $obj = new Compare();
                $obj -> File1 = 'secondbackup.sql';
                $obj -> File2 = 'firstbackup.sql';
                $obj -> ProcessFile();


    }
    else
    {
?>
Usage:
<p>
<b>Create First Backup: ?step=1</b>
<br>
<b>Create Second Backup: ?step=2</b>
<br>
<b>Compare Backups: ?step=final</b>
</p>
<?php
    }
?>
</body>
</html>
