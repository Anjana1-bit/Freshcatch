<?php require_once __DIR__.'/db.php';
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function url($path=''): string { return BASE_URL . '/' . ltrim($path,'/'); }
function redirect($path): never { header('Location: '.url($path)); exit; }
function send_security_headers(): void {
    if (headers_sent()) return;
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; script-src 'self'; object-src 'none'; base-uri 'self'; frame-ancestors 'self'; form-action 'self'");
}
function flash($key,$message=null) { if($message!==null){$_SESSION['flash'][$key]=$message;return;} $v=$_SESSION['flash'][$key]??null;unset($_SESSION['flash'][$key]);return $v; }
function csrf(): string { if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function verify_csrf(): void { if($_SERVER['REQUEST_METHOD']!=='POST'||!hash_equals($_SESSION['csrf']??'',$_POST['csrf']??'')){http_response_code(419);exit('Invalid form token. Please retry.');} }
function post($key,$default=''){ $value=$_POST[$key]??$default; return is_string($value)?trim($value):$default; }
function cart_count(): int { if(empty($_SESSION['user']['id']))return 0; $s=db()->prepare('SELECT COUNT(*) FROM cart_items ci JOIN carts c ON c.id=ci.cart_id WHERE c.user_id=?');$s->execute([$_SESSION['user']['id']]);return (int)$s->fetchColumn(); }
function seller_profile_id($userId){$s=db()->prepare('SELECT id FROM seller_profiles WHERE user_id=?');$s->execute([$userId]);return $s->fetchColumn();}
function status_class($s): string{return strtolower(str_replace(' ','-',$s));}
function delete_product_upload(?string $relativePath): void { if(!$relativePath||!str_starts_with($relativePath,'uploads/products/'))return; $file=UPLOAD_DIR.basename($relativePath);if(is_file($file))unlink($file); }
function upload_product_image(array $upload): ?string { if(empty($upload['name']))return null;$errors=[UPLOAD_ERR_INI_SIZE=>'Image exceeds the PHP upload limit.',UPLOAD_ERR_FORM_SIZE=>'Image exceeds the allowed size.',UPLOAD_ERR_PARTIAL=>'Image upload was incomplete.',UPLOAD_ERR_NO_TMP_DIR=>'Server upload folder is missing.',UPLOAD_ERR_CANT_WRITE=>'Server cannot write the image file.',UPLOAD_ERR_EXTENSION=>'Image upload was blocked by a PHP extension.'];if(($upload['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_OK)throw new Exception($errors[$upload['error']]??'Image upload failed.');if(($upload['size']??0)>MAX_UPLOAD)throw new Exception('Image must be 2 MB or smaller.');if(!is_uploaded_file($upload['tmp_name']))throw new Exception('Invalid image upload.');$type=mime_content_type($upload['tmp_name']);$extensions=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];if(!isset($extensions[$type])||@getimagesize($upload['tmp_name'])===false)throw new Exception('Only valid JPG, PNG, and WebP images are allowed.');if(!is_dir(UPLOAD_DIR)&&!mkdir(UPLOAD_DIR,0755,true))throw new Exception('Could not create the product image folder.');if(!is_writable(UPLOAD_DIR))throw new Exception('Product image folder is not writable. Ask the administrator to allow Apache write access to uploads/products.');$file=bin2hex(random_bytes(16)).'.'.$extensions[$type];if(!move_uploaded_file($upload['tmp_name'],UPLOAD_DIR.$file))throw new Exception('Could not save the image. Check uploads/products permissions.');return 'uploads/products/'.$file; }
