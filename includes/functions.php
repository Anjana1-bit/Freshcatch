<?php require_once __DIR__.'/db.php';
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function url($path=''): string { return BASE_URL . '/' . ltrim($path,'/'); }
function redirect($path): never { header('Location: '.url($path)); exit; }
function flash($key,$message=null) { if($message!==null){$_SESSION['flash'][$key]=$message;return;} $v=$_SESSION['flash'][$key]??null;unset($_SESSION['flash'][$key]);return $v; }
function csrf(): string { if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function verify_csrf(): void { if(!hash_equals($_SESSION['csrf']??'',$_POST['csrf']??'')){http_response_code(419);exit('Invalid form token. Please retry.');} }
function post($key,$default=''){return trim($_POST[$key]??$default);}
function cart_count(): int { if(empty($_SESSION['user']['id']))return 0; $s=db()->prepare('SELECT COUNT(*) FROM cart_items ci JOIN carts c ON c.id=ci.cart_id WHERE c.user_id=?');$s->execute([$_SESSION['user']['id']]);return (int)$s->fetchColumn(); }
function seller_profile_id($userId){$s=db()->prepare('SELECT id FROM seller_profiles WHERE user_id=?');$s->execute([$userId]);return $s->fetchColumn();}
function status_class($s): string{return strtolower(str_replace(' ','-',$s));}
function delete_product_upload(?string $relativePath): void { if(!$relativePath||!str_starts_with($relativePath,'uploads/products/'))return; $file=UPLOAD_DIR.basename($relativePath);if(is_file($file))unlink($file); }
