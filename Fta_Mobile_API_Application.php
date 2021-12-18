<?php


/*
Plugin Name: Fta Mobile Application API Creator
Description: Is Plug in For Reza Fta Mobile Applications
Author: Rezafta
Author URI: http://sorapp.ir
*/


//header('Content-Type: application/json');



if(isset($_REQUEST['Get_Action']))
{

    require("Main_Class.php");

    if($_REQUEST['Get_Action']=="Categorys")
    {
        DB::Categorys();
    }
    if($_REQUEST['Get_Action']=="Posts_Of_Category")
    {
        $Category_Id=$_REQUEST['Category_Id'];
        DB::Posts_Of_a_Categorys($Category_Id);
    }
    if($_REQUEST['Get_Action']=="Post")
    {
        $Post_Id=$_REQUEST['Post_Id'];
        DB::Post($Post_Id);
    }
    if($_REQUEST['Get_Action']=="Log")
    {
        $Log_Id=$_REQUEST['User_Id'];
        DB::Log($Log_Id);
    }
    if($_REQUEST['Get_Action']=="Version")
    {
        DB::Version();
    }
    if($_REQUEST['Get_Action']=="GET_ALL_POSTS")
    {
        DB::ALL_Posts();
    }
    if($_REQUEST['Get_Action']=="NEW_USER")
    {
        $USERNAME=$_REQUEST['USER_NAME'];
        $NameAndFamily=$_REQUEST['NameAndFamily'];


        $Email="";
        $Phone="";

        if(isset($_REQUEST['Email']))
        {
            $Email=$_REQUEST['Email'];
        }
        if(isset($_REQUEST['Phone']))
        {
            $Phone=$_REQUEST['Phone'];
        }

        $Date=date('Y-m-d h:i:s');

        $Regester_Key=rand(1000,9999);

        DB::NEW_USER($USERNAME,$NameAndFamily,$Email,$Date,$Regester_Key,$USERNAME,$Phone);
    }
    if($_REQUEST['Get_Action']=="Login_USER")
    {
        $USERNAME="";
        $NameAndFamily="";

        $Email="";
        $Phone="";
        $Username="";

        if(isset($_REQUEST['Email']))
        {
            $Email=$_REQUEST['Email'];
        }
        if(isset($_REQUEST['Phone']))
        {
            $Phone=$_REQUEST['Phone'];
        }
        if(isset($_REQUEST['USER_NAME']))
        {
            $Username=$_REQUEST['USER_NAME'];
        }
        if(isset($_REQUEST['NameAndFamily']))
        {
            $NameAndFamily=$_REQUEST['NameAndFamily'];
        }

        $Regester_Key=rand(1000,9999);

        DB::Login_USER($Regester_Key,$Phone,$Email,$NameAndFamily,$Username);
    }
    if($_REQUEST['Get_Action']=="USER_REGESTER")
    {
        if(isset($_REQUEST['CODE'])&&isset($_REQUEST['ID']))
        {
            $ID=$_REQUEST['ID'];
            $CODE=$_REQUEST['CODE'];
            DB::UPDATE_USER_Register($CODE,$ID);
        }
    }
    if($_REQUEST['Get_Action']=="GET_USER")
    {
        if(isset($_REQUEST['ID']))
        {
            $ID=$_REQUEST['ID'];
            DB::GET_USER_DITALES($ID);
        }
    }
    if($_REQUEST['Get_Action']=="SEARCH")
    {
        if(isset($_REQUEST['VALUE']))
        {
            $VALUE=$_REQUEST['VALUE'];
            DB::SEARCH_A_VALUE($VALUE);
        }
    }

}
else
{
    //echo "<script> window.document.location.href='http://google.com'; </script>";
}




?>