<?php // Ajax intake for bound form elements

// global $plog_level; $plog_level=1;
 include 'core/Page.php';

 if ( Session::logged_in() ) {
  global $database;
  $getpost=getpost();
  if ( isset($getpost['I']) && isset($getpost['L']) && isset($getpost['T']) && isset($getpost['F']) ) {
   $ID=$getpost['I'];
   $Table=$getpost['T'];
   $Field=$getpost['F'];
   $List=$getpost['L'];
   if ( !Auth::ACL('edit-'.$Table) && !Auth::ACL('edit-'.$Table.'-'.$Field) && !Auth::ACL('su') ) { echo '{"result":"readonly"}'; die; }
   if ( LockCheck( $Table, $ID ) === TRUE ) { echo '{"result":"locked"}'; die; }
   if ( AutoLockCheck( $Table, $ID ) === TRUE ) { echo '{"result":"locked"}'; die; }
   // Update the db, but only when a valid model is provided
   if ( class_exists($Table) && matches(get_parent_class($Table),'Model') ) {
    $model=new $Table($database);
    $model->Update( array( $Field=>$List ), array ( 'ID'=>$ID ) ) ;
    Modified( array( "D"=>array($Table=>array("F"=>$Field,"I"=>$ID))) );
    echo '{"result":"success"}';
    exit;
   }
  }
 }

 echo '{"result":"error"}';
