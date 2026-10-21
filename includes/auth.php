<?php require_once __DIR__.'/functions.php';
function user(){ return $_SESSION['user'] ?? null; }
function require_login(){
    if (!user()) { flash('error','Please sign in to continue.'); redirect('login.php'); }
    $s = db()->prepare('SELECT id,name,email,role,status FROM users WHERE id=? LIMIT 1');
    $s->execute([user()['id']]);
    $live = $s->fetch();
    if (!$live || $live['status'] !== 'active') {
        $_SESSION = []; session_destroy();
        flash('error','Your session is no longer active. Please sign in again.'); redirect('login.php');
    }
    $_SESSION['user'] = ['id'=>$live['id'],'name'=>$live['name'],'email'=>$live['email'],'role'=>$live['role']];
}
function require_role($role){ require_login(); if(user()['role'] !== $role){ flash('error','You do not have access to that page.'); redirect('index.php'); } }
function login_user($u){ session_regenerate_id(true); $_SESSION['csrf']=bin2hex(random_bytes(32)); $_SESSION['user']=['id'=>$u['id'],'name'=>$u['name'],'email'=>$u['email'],'role'=>$u['role']]; }
