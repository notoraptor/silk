<?php
require_once('server_infos.php');
require_once('priv/utils.php');
require_once('priv/template.php');
$db = new Database();
$models = $db->models();
$models_with_articles = get_models_for_articles($models);
$data = new FrontData($db);
$config = $data->getConfig();
$attention_message = '';
$attention_type = '';
if (!empty($_POST)) {
	$name = utils_s_post('name');
	$email = utils_s_post('email');
	$phone = utils_s_post('phone');
	$message = utils_s_post('message');
	if (!$name) {
		$attention_message = 'Missing required name.';
		$attention_type = 'error';
	} else if (!$message) {
		$attention_message = 'Missing required message.';
		$attention_type = 'error';
	} else if (!$email && !$phone) {
		$attention_message = 'Missing either email or phone.';
		$attention_type = 'error';
	} else if ($email && !utils_valid_email($email)) {
		$attention_message = 'Invalid given email.';
		$attention_type = 'error';
	} else {
        $message = str_replace("\r\n", "<br/>", $message);
        $message = str_replace("\n", "<br/>", $message);
        $message = str_replace("\r", "<br/>", $message);
		$subject = 'Conact request';
		$body = '';
		capture_start();
		if (!$email)
			$email = '(none)';
		if (!$phone)
			$phone = '(none)';
		?>
		<div>
			<h1>SILK / Contact request (<?php echo date('d/m/Y - H:i:s');?>)</h1>
			<table>
				<tr><td><strong>Name:</strong></td><td><?php echo $name;?></td></tr>
				<tr><td><strong>Email:</strong></td><td><?php echo $email;?></td></tr>
				<tr><td><strong>Phone:</strong></td><td><?php echo $phone;?></td></tr>
				<tr><td><strong>Message:</strong></td><td><?php echo $message;?></td></tr>
			</table>
		</div>
		<?php
		capture_end($body);
		$sent = utils_mail($config->site_email, $subject, $body);
		if ($sent) {
			$attention_message = 'Your message is correctly sent. We will contact you soon. Thanks!';
			$attention_type = 'success';
		} else {
			$attention_message = 'Error while sending your request. Please retry later!';
			$attention_type = 'error';
		}
	}
}
capture_start();
?>
<div class="about"><?php echo $config->about_page_text; ?></div>
<?php if($attention_message) { ?>
<div class="p-2 message-<?php echo $attention_type;?>"><?php echo $attention_message;?></div>
<?php }; ?>
<div class="row align-items-center">
    <div class="col-md-7">
        <form method="post" class="mt-5">
            <fieldset class="form-group">
                <legend>Contact us</legend>
                <div class="form-row">
                    <div class="form-group col-sm">
                        <input type="text" name="name" class="form-control" placeholder="Name" value="<?php echo utils_s_post('name', '');?>"/>
                    </div>
                    <div class="form-group col-sm">
                        <input type="email" name="email" class="form-control" placeholder="Email" value="<?php echo utils_s_post('email', '');?>"/>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col">
                        <input type="text" name="phone" class="form-control" placeholder="Phone Number"  value="<?php echo utils_s_post('phone', '');?>"/>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col">
                        <textarea class="form-control" name="message" placeholder="Message"><?php echo utils_s_post('message', '');?></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-dark px-4">Send</button>
                </div>
            </fieldset>
        </form>
    </div>
    <div class="col-md text-center mb-5 mb-md-0 text-md-right"><?php echo $config->about_contact_text;?></div>
</div>

<?php if (count($models_with_articles)) {
    ?>
    <div class="discover-more">
        <div>DISCOVER MORE</div>
        <div>&#8226;&#8226;&#8226;</div>
    </div>
    <?php
    echo print_models_for_articles($models_with_articles);
} ?>
<?php
capture_end($data->content);
$data->title = 'ABOUT';
$data->pagename = 'about';
echo template($data);
?>