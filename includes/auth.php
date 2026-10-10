<?php require_once __DIR__.'/functions.php';
function user(){return $_SESSION['user']??null;}
function require_login(){if(!user()){flash('error','Please sign in to continue.');redirect('login.php');}}
function require_role($role){require_login();if(user()['role']!==$role){flash('error','You do not have access to that page.');redirect('index.php');}}
function login_user($u){$_SESSION['user']=['id'=>$u['id'],'name'=>$u['name'],'email'=>$u['email'],'role'=>$u['role']];session_regenerate_id(true);}
