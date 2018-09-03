<?php
require_once('server_infos.php');
require_once('priv/utils.php');
require_once('priv/template.php');
$db = new Database();
$data = new FrontData($db);
$config = $data->getConfig();
$attention_message = '';
$attention_type = '';
if (!empty($_POST)) {
	$fields = array();
	$required_fields = array(
		'first-name' => 'first name', 'last-name' => 'last name', 'address' => 'address', 'city-town' => 'city/town',
		'postcode' => 'post code', 'date-birth' => 'date of birth', 'hair' => 'hair colour', 'eyes' => 'eye colour',
		'height' => 'height', 'waist' => 'waist', 'bust' => 'bust', 'dress' => 'dress', 'hips' => 'hips',
		'shoe' => 'shoe size', 'accept-policy' => 'you must accept Information to the Applicant'
	);
	$special_fields = array('email', 'phone');
	$file_fields = array('file-1', 'file-2', 'file-3');
	$fields['accept-data-sharing'] = utils_s_post('accept-data-sharing');
	foreach($required_fields as $fiel_name => $field_title) {
		$field_value = utils_s_post($fiel_name, false);
		if ($field_value || $field_value === "0") {
			$fields[$field_name] = $field_value;
		} else {
			$attention_message = 'Missing field: '.$field_title;
			$attention_type = 'error';
		}
	}
	if (!$attention_type) {
		$fields['email'] = utils_s_post('email');
		$fields['phone'] = utils_s_post('phone');
		if (!$fields['email'] && !$fields['phone']) {
			$attention_message = 'Missing either email or phone.';
			$attention_type = 'error';
		} else if ($fields['email'] && !utils_valid_email($fields['email'])) {
			$attention_message = 'Invalid given email.';
			$attention_type = 'error';
		}
	}
	if (!$attention_type) {
		foreach ($file_fields as $file_field_name) {
			if(isset($_FILES[$file_field_name]) && $_FILES[$file_field_name]['name']) {
				$upload_folder = server_dir().'/uploads';
				if (!file_exists($upload_folder))
					mkdir($upload_folder);
				$ret = utils_upload($file_field_name, server_dir().'uploads');
				$uploaded_file_path = $ret['file'];
				$error_message = $ret['error'];
				if($uploaded_file_path) {
					$fields[$file_field_name] = $uploaded_file_path;
				}
				else {
					$attention_message = 'Error when uploading file '.$file_field_name.': '.$error_message;
					$attention_type = 'error';
				}
			}
		}
	}
	if (!$attention_type) {
		$subject = 'Submission request';
		$body = '';
		$field_names_to_print = array(
			'first-name', 'last-name', 'email', 'phone', 'address', 'city-town',
			'postcode', 'date-birth', 'hairs', 'eyes',
			'height', 'waist', 'bust', 'dress', 'hips',
			'shoe'
		);
		$fields_titles = array(
			'first-name' => 'first name', 'last-name' => 'last name', 'email' => 'Email', 'Phone' => 'phone', 'address' => 'address', 'city-town' => 'city/town',
			'postcode' => 'post code', 'date-birth' => 'date of birth', 'hairs' => 'hairs', 'eyes' => 'eyes',
			'height' => 'height', 'waist' => 'waist', 'bust' => 'bust', 'dress' => 'dress', 'hips' => 'hips',
			'shoe' => 'shoes', 'file-1' => 'File 1', 'file-2' => 'File 2', 'file-3' => 'File 3'
		);
		capture_start();
		?>
		<div>
			<h1>Submission request</h1>
			<table>
				<?php
				foreach($field_names_to_print as $field_name_to_print) {
					$title = $fields_titles[$field_name_to_print];
					$value = isset($fields[$field_name_to_print]) && $fields[$field_name_to_print] ? $fields[$field_name_to_print] : '(none)';
					?>
					<tr><td><?php echo $title;?>:</td><td><?php echo $value;?></td></tr>
					<?php
					foreach($file_fields as $file_field_name) {
						if (isset($fields[$file_field_name])) {
							$file_url = str_replace(server_dir(), server_http(), $fields[$file_field_name]);
							?>
							<tr><td><?php echo $fields_titles[$file_field_name];?>:</td><td><a href="<?php echo $file_url;?>"><?php echo $file_url;?></a></td></tr>
							<?php
						}
					}
				}
				?>
				<tr><td>Accept data sharing?</td><td><?php echo (isset($fields['accept-data-sharing']) && $fields['accept-data-sharing'] ? 'Yes' : 'No');?></td></tr>
			</table>
		</div>
		<?php
		capture_end($body);
		$sent = utils_mail($config->site_email, $subject, $body);
		if ($sent) {
			$attention_message = 'Your application was correctly submitted. We will contact you soon. Thanks!';
			$attention_type = 'success';
		} else {
			$attention_message = 'Error while sending your request. Please retry later!';
			$attention_type = 'error';
		}
	}
}
capture_start();
?>
	<p class="submission">COULD YOU BE A SILK MODEL ?</p>
	<?php if($attention_message) { ?>
	<p class="p-2 message-<?php echo $attention_type;?>"><?php echo $attention_message;?></p>
	<?php }; ?>
	<div class="row">
		<div class="col-sm"><?php echo $config->submission_page_text; ?></div>
		<div class="col-sm">
			<form method="post" enctype="multipart/form-data">
				<div class="form-row">
					<div class="form-group col-sm">
						<input type="text" name="first-name" class="form-control" placeholder="First name" value="<?php echo utils_s_post('first-name', '');?>"/>
					</div>
					<div class="form-group col-sm">
						<input type="text" name="last-name" class="form-control" placeholder="Last name" value="<?php echo utils_s_post('last-name', '');?>"/>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-sm">
						<input type="text" name="email" class="form-control" placeholder="Email" value="<?php echo utils_s_post('email', '');?>"/>
					</div>
					<div class="form-group col-sm">
						<input type="text" name="phone" class="form-control" placeholder="Telephone" value="<?php echo utils_s_post('phone', '');?>"/>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col">
						<textarea class="form-control" name="address" placeholder="Address"><?php echo utils_s_post('address', '');?></textarea>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-sm">
						<input type="text" name="city-town" class="form-control" placeholder="City/Town" value="<?php echo utils_s_post('city-town', '');?>"/>
					</div>
					<div class="form-group col-sm">
						<input type="text" name="postcode" class="form-control" placeholder="Postcode" value="<?php echo utils_s_post('postcode', '');?>"/>
					</div>
					<div class="form-group col-sm">
						<input type="text" name="date-birth" class="form-control" placeholder="Date of Birth" value="<?php echo utils_s_post('date-birth', '');?>"/>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-sm">
						<input type="text" name="hair" class="form-control" placeholder="Hair colour" value="<?php echo utils_s_post('hair', '');?>"/>
					</div>
					<div class="form-group col-sm">
						<input type="text" name="eyes" class="form-control" placeholder="Eye colour" value="<?php echo utils_s_post('eyes', '');?>"/>
					</div>
					<div class="form-group col-sm">
						<input type="text" name="height" class="form-control" placeholder="Height" value="<?php echo utils_s_post('height', '');?>"/>
					</div>
					<div class="form-group col-sm">
						<input type="text" name="waist" class="form-control" placeholder="Waist" value="<?php echo utils_s_post('waist', '');?>"/>
					</div>
				</div>
				<div class="form-row">
					<div class="form-group col-sm">
						<input type="text" name="bust" class="form-control" placeholder="Bust" value="<?php echo utils_s_post('bust', '');?>"/>
					</div>
					<div class="form-group col-sm">
						<input type="text" name="dress" class="form-control" placeholder="Dress" value="<?php echo utils_s_post('dress', '');?>"/>
					</div>
					<div class="form-group col-sm">
						<input type="text" name="hips" class="form-control" placeholder="Hips" value="<?php echo utils_s_post('hips', '');?>"/>
					</div>
					<div class="form-group col-sm">
						<input type="text" name="shoe" class="form-control" placeholder="Shoe size" value="<?php echo utils_s_post('shoe', '');?>"/>
					</div>
				</div>
				<div>IMAGES (UP TO 2MB EACH)</div>
				<div class="form-row">
					<div class="form-group col-sm">
						<div class="custom-file col-sm">
							<input type="file" class="custom-file-input" name="file-1" id="file-1" />
							<label class="custom-file-label" for="file-1">Choose file...</label>
						</div>
					</div>
					<div class="form-group col-sm">
						<div class="custom-file col-sm">
							<input type="file" class="custom-file-input" name="file-2" id="file-2" />
							<label class="custom-file-label" for="file-2">Choose file...</label>
						</div>
					</div>
					<div class="form-group col-sm">
						<div class="custom-file col-sm">
							<input type="file" class="custom-file-input" name="file-3" id="file-3" />
							<label class="custom-file-label" for="file-3">Choose file...</label>
						</div>
					</div>
				</div>
				<div class="form-check">
					<input type="checkbox" class="form-check-input" name="accept-policy" id="accept-policy"/>
					<label class="form-check-label" for="accept-policy">I read and I accept the Information to the Applicant.</label>
				</div>
				<div class="form-check">
					<input type="checkbox" class="form-check-input" name="accept-data-sharing" id="accept-data-sharing"/>
					<label class="form-check-label" for="accept-data-sharing"><?php echo $config->submission_page_data_sharing_text;?></label>
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-dark">Submit</button>
				</div>
			</form>
		</div>
	</div>
<?php
capture_end($data->content);
$data->title = 'SUBMISSION';
$data->pagename = 'submission';
echo template($data);
?>