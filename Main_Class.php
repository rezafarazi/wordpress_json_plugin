<?php

class DB
{

    public static function Categorys()
    {
        require("../../../wp-config.php");

        try
        {
            $Connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD,DB_NAME);

            mysqli_set_charset($Connection,"utf8");

            $Table1=$table_prefix.'terms';
            $Table2=$table_prefix.'term_relationships';

            //$SQL="SELECT * FROM $Table1 INNER JOIN $Table2 ON $Table1.term_id=$Table2.object_id;";

            $SQL="SELECT * FROM $Table1;";

            $Result=$Connection->query($SQL);

            $Json=array();

            if($Result->num_rows>0)
            {
                while ($row = $Result->fetch_assoc())
                {
                    $Js=array('Id'=>$row['term_id'],'Name'=>$row['name']);
                    array_push($Json,$Js);
                }
            }

            print_r(json_encode($Json,JSON_UNESCAPED_UNICODE));


        }
        catch (Exception $Err)
        {
            echo $Err->getMessage();
        }

    }

    public static function Posts_Of_a_Categorys($Category_Id)
    {
        require("../../../wp-config.php");
        try
        {
            $Connection=new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
            mysqli_set_charset($Connection,'utf8');

            $Table1=$table_prefix.'posts';
            $Table2=$table_prefix.'term_relationships';

            $Result=$Connection->query("SELECT ID,post_title From $Table1 Inner Join $Table2 On $Table1.id=$Table2.object_id WHERE $Table2.term_taxonomy_id=$Category_Id AND post_type Like 'post' AND NOT post_title='' AND post_status LIKE 'publish' AND post_type LIKE 'post' ORDER BY ID DESC;");

            $Json=array();
            if($Result->num_rows>0)
            {
                while($row=$Result->fetch_assoc())
                {
                    $Id=$row['ID'];
                    $Js=array('Id'=>$Id,'Image'=>self::GET_POST_IMAGE_BY_ID($Id),'Title'=>$row['post_title']);
                    array_push($Json,$Js);
                }
            }

            print_r(json_encode($Json,JSON_UNESCAPED_UNICODE ));

        }
        catch (Exception $Err)
        {
            echo $Err->getMessage();
        }
    }

    public static function ALL_Posts()
    {
        require("../../../wp-config.php");
        try
        {
            $Connection=new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
            mysqli_set_charset($Connection,"utf8");

            $Table1=$table_prefix.'posts';

            $Result=$Connection->query("SELECT ID,post_title FROM $Table1 WHERE post_type Like 'post' AND NOT post_title='' AND post_status LIKE 'publish' AND post_type LIKE 'post';");

            $Json=array();
            if($Result->num_rows>0)
            {
                while($row=$Result->fetch_assoc())
                {
                    $Id=$row['ID'];
                    $Js=array('Id'=>$Id,'Image'=>self::GET_POST_IMAGE_BY_ID($Id),'Title'=>$row['post_title']);
                    array_push($Json,$Js);
                }
            }

            $Json=array_reverse($Json);

            print_r(json_encode($Json,JSON_UNESCAPED_UNICODE));

        }
        catch (Exception $Err)
        {
            echo $Err->getMessage();
        }
    }

    public static function GET_POST_IMAGE_BY_ID($ID)
    {
        require("../../../wp-config.php");
        try
        {
            $Connection=new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
            mysqli_set_charset($Connection,'utf8');

            $Table1=$table_prefix.'posts';
            $Table2=$table_prefix.'postmeta';

            $Result=$Connection->query("SELECT guid FROM $Table2 INNER JOIN $Table1 ON $Table2.meta_value=$Table1.ID WHERE $Table2.meta_key LIKE '_thumbnail_id' AND $Table2.post_id=$ID");

            $Js="";

            if($Result->num_rows>0)
            {
                while($row=$Result->fetch_assoc())
                {
                    $Js=$row['guid'];
                }
            }

            return $Js;

        }
        catch (Exception $Err)
        {
            echo $Err->getMessage();
        }

        return "";
    }

    public static function Post($Post_Id)
    {
        require("../../../wp-config.php");
        try
        {
            $Connection=new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
            mysqli_set_charset($Connection,'utf8');
            $Table=$table_prefix.'posts';
            $Result=$Connection->query("SELECT * FROM $Table WHERE id=$Post_Id");

            if($Result->num_rows>0)
            {
                while($row=$Result->fetch_assoc())
                {
                    $Js=array('Post'=>$row['post_content']);
                    print_r(json_encode($Js,JSON_UNESCAPED_UNICODE ));
                }
            }
        }
        catch (Exception $Err)
        {
            echo $Err->getMessage();
        }
    }

    public static function Log($User_ID)
    {
        require("../../../wp-config.php");
        try
        {
            $Connection=new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
            mysqli_set_charset($Connection,"utf8");
            try
            {
                $Table=$table_prefix."logs";
                $Table1=$table_prefix."users";
                $Connection->query("ALTER TABLE $Table1 ADD `Phone` VARCHAR(255) NULL , ADD `Condition` VARCHAR(255) NULL ;");
                $Connection->query("CREATE TABLE IF NOT EXISTS $Table(`id` int(11) NOT NULL AUTO_INCREMENT,`Date` varchar(255) NOT NULL,`Time` varchar(255) NOT NULL,`USER` INT NOT NULL,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
            }
            catch (Exception $Err)
            {

            }

            $Res=self::GET_USER($User_ID);

            if($Res=="NOT_FOUND")
            {
                print_r(json_encode(array('Condition' => 'NOT_FOUND')));
            }
            else if($Res=="NOT_ACCESS")
            {
                print_r(json_encode(array('Condition' => 'Access_Is_Limited')));
            }
            else if($Res=="SERVER_FIXING")
            {
                print_r(json_encode(array('Condition' => 'Server_Is_FIXING')));
            }
            else if($Res=="LIMITED_OF_SERVER")
            {
                print_r(json_encode(array('Condition' => 'LIMITED_OF_SERVER')));
            }
            else
            {
                print_r(json_encode(array('Condition' => 'Welcome')));
                $Result = $Connection->query("INSERT INTO $Table (`Date`, `Time`, `USER`) VALUES ('" . date("Y/m/d") . "', '" . date("h:i:sa") . "', $Res);");
            }
        }
        catch (Exception $Err)
        {
            echo $Err->getMessage();
        }
    }

    public static function Version()
    {
        require("../../../wp-config.php");
        try
        {
            $Connection=new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
            mysqli_set_charset($Connection,"utf8");
            try
            {
                $Table=$table_prefix."Version";
                $Res=$Connection->query("CREATE Table $Table(NUM nvarchar(255),URL nvarchar(255));");

                if($Res!="")
                    $Connection->query("Insert into $Table Values('1','http://sorapp.ir/app');");
            }
            catch (Exception $Err)
            {

            }

            $Result=$Connection->query("SELECT * FROM $Table;");

            $Version="";
            $URL="";

            if($Result->num_rows>0)
            {
                while($row=$Result->fetch_assoc())
                {
                    $Version=$row['NUM'];
                    $URL=$row['URL'];
                }
            }

            print_r(json_encode(array('Version'=>$Version,'URL'=>$URL)));
        }
        catch (Exception $Err)
        {
            echo $Err->getMessage();
        }
    }

    public static function GET_USER($USER_ID)
    {
        require("../../../wp-config.php");
        try
        {
            $Table=$table_prefix."users";
            $Connection=new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
            mysqli_set_charset($Connection,"utf8");
            $Result=$Connection->query("SELECT * FROM $Table WHERE user_email='$USER_ID' OR Phone='$USER_ID' OR ID=$USER_ID OR user_login='$USER_ID';");

            if($Result->num_rows>0)
            {
                while($row=$Result->fetch_assoc())
                {
                    if($row['Condition']!=null)
                    {
                        return $row['Condition'];
                    }
                    else
                    {
                        return $row['ID'];
                    }
                }
            }

        }
        catch (Exception $Err)
        {

        }

        return "NOT_FOUND";
    }

    public static function NEW_USER($user_login,$user_nicename,$user_email,$user_registered,$user_activation_key,$Phone)
    {
        require("../../../wp-config.php");

        if(self::GET_USER($user_email)=="NOT_FOUND" || self::GET_USER($Phone)=="NOT_FOUND")
        {
            try
            {
                $Connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
                mysqli_set_charset($Connection, 'utf8');
                $Table = $table_prefix . "users";
                $Connection->query("INSERT INTO $Table (`user_login`,`user_nicename`, `user_email`, `user_registered`, `user_activation_key`, `display_name`, `Phone`, `Condition`) VALUES ('$user_login', '$user_nicename','$user_email', '$user_registered',$user_activation_key, '$user_nicename', '$Phone','NOT_ACCESS');");

                $SQL="";
                if(trim($Phone!=""))
                {
                    $SQL.="Phone='$Phone'";
                }
                if(trim($user_email!=""))
                {
                    if($SQL!="")
                    {
                        $SQL.="OR user_email='$user_email'";
                    }
                    else
                    {
                        $SQL.="user_email='$user_email'";
                    }
                }

                $Result = $Connection->query("SELECT * FROM $Table WHERE $SQL;");

                if ($Result->num_rows > 0)
                {
                    while ($row = $Result->fetch_assoc())
                    {
                        print_r(json_encode(array('Condition' => 'yes', 'Id' => $row['ID'], 'Register_Code' => $user_activation_key)));
                        self::SEND_VERIFY_MAIL($user_activation_key,$user_email);
                        return;
                    }
                }
            }
            catch (Exception $Err)
            {

            }
        }
        else
        {
            print_r(json_encode(array('Condition'=>'USER_EXIST')));
        }
    }

    public static function Login_USER($RAND,$Phone,$Email,$NameAndFamily,$Username)
    {
        require("../../../wp-config.php");
        try
        {
            $Table=$table_prefix."users";
            $Connection=new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
            mysqli_set_charset($Connection,"utf8");

            $SQL="";
            if($Phone!="")
            {
                $SQL.="Phone='$Phone'";
            }
            if($Email!="")
            {
                if($SQL!="")
                {
                    $SQL.=" OR user_email='$Email'";
                }
                else
                {
                    $SQL.="user_email='$Email'";
                }
            }
            if($NameAndFamily!="")
            {
                if($SQL!="")
                {
                    $SQL.=" OR user_nicename='$NameAndFamily'";
                }
                else
                {
                    $SQL.="user_nicename='$NameAndFamily'";
                }
            }
            if($Username!="")
            {
                if($SQL!="")
                {
                    $SQL.=" OR user_login='$Username'";
                }
                else
                {
                    $SQL.="user_login='$Username'";
                }
            }


            $SQL.=" AND $Table.Condition!='LIMITED_OF_SERVER'";

            //print_r("SELECT * FROM $Table WHERE $SQL;");
            $Id="";
            $Em="";
            $Phn="";

            $Result=$Connection->query("SELECT * FROM $Table WHERE $SQL;");
            if ($Result->num_rows > 0)
            {
                while ($row = $Result->fetch_assoc())
                {
                    $Id=$row['ID'];
                    $Em=$row['user_email'];
                    $Phn=$row['Phone'];
                    break;
                }
            }
            else
            {
                print_r(json_encode(array('Condition'=>'Not_Found')));
                return;
            }


            if($Id!="")
            {
                $Connection->query("UPDATE $Table SET user_activation_key=$RAND WHERE ID=$Id;");
                print_r(json_encode(array('Condition' => 'yes', 'Id' => $row['ID'], 'Register_Code' => $RAND,'Email'=>$Em,'Phone'=>$Phn)));

                if($Em!="")
                    self::SEND_VERIFY_MAIL($RAND,$Em);

                return;
            }


        }
        catch (Exception $Err)
        {

        }

        print_r(json_encode(array("Condition"=>"LIMITED_BY_SERVER")));

    }

    public static function UPDATE_USER_Register($CODE,$ID)
    {
        require("../../../wp-config.php");
        try
        {
            $Table=$table_prefix."users";
            $Connection=new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
            mysqli_set_charset($Connection,"utf8");
            $ACTIVE=false;
            $Result=$Connection->query("SELECT * FROM $Table WHERE ID=$ID AND $Table.Condition!='LIMITED_OF_SERVER' AND user_activation_key='$CODE';");
            if ($Result->num_rows > 0)
            {
                while ($row = $Result->fetch_assoc())
                {
                    $ACTIVE=true;
                    break;
                }
            }


            if($ACTIVE)
            {
                $Connection->query("UPDATE $Table SET $Table.user_activation_key='',$Table.Condition=NULL WHERE ID=$ID AND user_activation_key=$CODE;");
                print_r(json_encode(array("Condition"=>"YES")));
                return;
            }

        }
        catch (Exception $Err)
        {

        }

        print_r(json_encode(array("Condition"=>"NO")));

    }

    public static function SEND_VERIFY_MAIL($Rand,$Email)
    {
        $msg = "Sorapp.ir Application Verify Code Is : ".$Rand;

        $msg = wordwrap($msg,70);

        mail("$Email","Sorapp.ir",$msg);
    }

    public static function GET_USER_DITALES($USER_ID)
    {
        require("../../../wp-config.php");
        try
        {
            $Table=$table_prefix."users";
            $Connection=new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
            mysqli_set_charset($Connection,"utf8");
            $Result=$Connection->query("SELECT * FROM $Table WHERE ID=$USER_ID;");

            $Js=null;

            if($Result->num_rows>0)
            {
                while($row=$Result->fetch_assoc())
                {

                    $json=array(
                        "ID"=>$row['ID'],
                        "Username"=>$row['user_login'],
                        "FullName"=>$row['user_nicename'],
                        "Phone"=>$row['Phone'],
                        "Email"=>$row['user_email'],
                        "Image"=>get_avatar_url( $USER_ID ),
                        "Start_Date"=>$row['user_registered']
                    );

                    $Js=$json;
                }
            }

            print_r(json_encode($Js));

        }
        catch (Exception $Err)
        {

        }
    }

    public static function SEARCH_A_VALUE($SEARCH_VALUE)
    {
        require("../../../wp-config.php");
        try
        {
            $Table=$table_prefix."posts";
            $Connection=new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
            mysqli_set_charset($Connection,"utf8");

            $Result=$Connection->query("SELECT * FROM $Table WHERE (post_content LIKE '%$SEARCH_VALUE%' OR post_title LIKE '%$SEARCH_VALUE%') AND post_type Like 'post' AND NOT post_title='' AND post_status LIKE 'publish' AND post_type LIKE 'post';");

            $Json=array();

            if($Result->num_rows>0)
            {
                while($row=$Result->fetch_assoc())
                {
                    $Id=$row['ID'];
                    $Js=array('Id'=>$Id,'Image'=>self::GET_POST_IMAGE_BY_ID($Id),'Title'=>$row['post_title']);
                    array_push($Json,$Js);
                }
            }

            print_r(json_encode($Json,JSON_UNESCAPED_UNICODE ));

        }
        catch (Exception $Err)
        {

        }
    }

}

?>