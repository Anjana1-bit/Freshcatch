<?php require 'includes/auth.php';
if(user()) redirect(user()['role'].'/dashboard.php');
$error='';
$attempts=$_SESSION['login_attempts']??['count'=>0,'until'=>0];
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    if($attempts['until']>time()){
        $remaining=(int)ceil(($attempts['until']-time())/60);
        $error="Too many sign-in attempts. Please wait {$remaining} minute(s).";
    }else{
        $email=filter_var(post('email'),FILTER_VALIDATE_EMAIL);
        $pass=$_POST['password']??'';
        if(!$email||!is_string($pass)||$pass==='') $error='Enter a valid email and password.';
        else {
            $s=db()->prepare('SELECT * FROM users WHERE email=? LIMIT 1');$s->execute([$email]);$u=$s->fetch();
            if(!$u||!password_verify($pass,$u['password_hash'])){
                $attempts['count']++;
                if($attempts['count']>=5){$attempts=['count'=>0,'until'=>time()+900];$error='Too many sign-in attempts. Please wait 15 minutes.';}
                else $error='Incorrect email or password.';
                $_SESSION['login_attempts']=$attempts;
            }elseif($u['status']!=='active') $error='This account is suspended.';
            else {unset($_SESSION['login_attempts']);login_user($u);flash('success','Welcome back, '.$u['name'].'!');redirect($u['role'].'/dashboard.php');}
        }
    }
}
$pageTitle='Sign in';require 'includes/header.php';?><div class="auth"><div><p class="eyebrow">WELCOME BACK</p><h1>Sign in to FreshCatch</h1><p>Access your orders, shop, and marketplace account.</p></div><form method="post" class="card-form" autocomplete="on"><input type="hidden" name="csrf" value="<?=csrf()?>"><?php if($error):?><p class="field-error"><?=e($error)?></p><?php endif;?><label>Email<input type="email" name="email" autocomplete="email" required></label><label>Password<input type="password" name="password" autocomplete="current-password" minlength="8" required></label><button>Sign in</button><p>New here? <a href="register.php">Create an account</a></p></form></div><?php require 'includes/footer.php'; ?>
