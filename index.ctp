<?php
$appController = new AppController();
?>
<div class="bg_wrap">
	<header class="app">
		<h1><?= $contest["contest_name"] ?></h1>
	</header>

	<div class="wrap">
		<h2>応募フォーム</h2>
		<div class="wrap_in">
			<p class="caution">以下のフォームに記載の上、「確認画面へ」ボタンを押してください。</p>
<?php
$limit = '2019-02-22 08:00:00';
if (date('U') <= strtotime($limit)) {
?>
			<p id="msg_title" style="cursor: pointer; font-weight: bold; color: #9E2424; text-align: center; font-size: 90%; margin-top: 1.5em;">※システムメンテナンスによる応募受付一時停止のお知らせ</p>
			<div id="msg_content" style="margin: 1em 0; padding: 1em; border: 7px solid #9E2424; background-color: #ffffff; display:none;">
				<p style="margin: 1em 0;">
					この度、システム増強に伴いメンテナンスを実施させていただくことになりました。<br>
					メンテナンスの時間帯は無料掲載申込のシステムがご利用いただけませんので、あらかじめご了承ください。<br>
				</p>
				<p style="margin: 1.5em 0;">
					<strong>■メンテナンス期間</strong><br>
					2019年02月22日（金） 04時00分 ～ 08時00分
				</p>
				<p>
					ご不便をお掛け致しますが、ご理解の程何卒よろしくお願い致します。
				</p>
			</div>
<?php } ?>

			<? if(isset($validationErrors) && count($validationErrors)){ ?>
				<div id="errorblock">
					<p class="err_tit">【入力エラー】以下の項目をご確認ください。</p>
					<ul>
						<? foreach((array)$validationErrors as $err){ ?>
							<? if(is_array($err)){ ?>
								<? foreach((array)$err as $err2){ ?>
									<li><?= $err2 ?></li>
								<? } ?>
							<? }else{ ?>
								<li><?= $err ?></li>
							<? } ?>
						<? } ?>
					</ul>
				</div>
			<? } ?>

			<form action="<?= Router::url() ?>" method="POST" enctype="multipart/form-data">
				<input type="hidden" name="member_id" value="<?= $member_id ?>">
				<input type="hidden" name="id" value="<?= $this->request->data('id') ?>">
				<table>
					<tbody>
						<? if($contest['fm_penname_disp']){ ?>
							<? if($contest['style_type'] == 0 ){ ?>
								<tr>
									<th>ペンネーム<? if($contest['fm_penname_req']){ ?><span class="required">必須</span><? } ?></th>
									<td>
										<p class="mb10"><input type="text" name="penname" style="background-color: #ffeeee" value="<?= $this->request->data('penname') ?>" placeholder="ペンネーム" class="<?= $appController->error($validationErrors, 'penname'); ?>"></p>
										<p class="ate" style="font-size: 1em; font-weight: bold; color: #ff0000">※掲載用のニックネーム、ペンネームです。<br>本名を出さない場合は必ず記載ください</p>
									</td>
								</tr>
							<? }elseif($contest['style_type'] == 1 ){ ?>
								<tr>
									<th>作家名<? if($contest['fm_penname_req']){ ?><span class="required">必須</span><? } ?></th>
									<td>
										<p class="mb10"><input type="text" name="penname" style="background-color: #ffeeee" value="<?= $this->request->data('penname') ?>" placeholder="作家名" class="<?= $appController->error($validationErrors, 'penname'); ?>"></p>
										<p class="ate" style="font-size: 1em; font-weight: bold; color: #ff0000">※掲載用の名前です。会社名で応募される場合はこちらに入力し、<br>担当者名は以下の「お名前」で入力してください。</p>
									</td>
								</tr>

							<? } ?>
						<? } ?>
						<? if($contest['fm_name_disp']){ ?>
							<tr>
								<th>お名前<? if($contest['fm_name_req']){ ?><span class="required">必須</span><? } ?></th>
								<td>
									<ul class="name">
										<li class="text">姓</li>
										<li><input type="text" name="name_sei" value="<?= $this->request->data('name_sei') ?>" placeholder="例：公募" class="<?= $appController->error($validationErrors, 'name_sei'); ?>"></li>
										<li class="text">名</li>
										<li><input type="text" name="name_mei" value="<?= $this->request->data('name_mei') ?>" placeholder="例：太郎" class="<?= $appController->error($validationErrors, 'name_mei'); ?>"></li>
									</ul>
								</td>
							</tr>
						<? } ?>
						<? if($contest['fm_kana_disp']){ ?>
							<tr>
								<th>お名前（カナ）<? if($contest['fm_kana_req']){ ?><span class="required">必須</span><? } ?></th>
								<td>
									<ul class="name">
										<li class="text">姓</li>
										<li><input type="text" name="kana_sei" value="<?= $this->request->data('kana_sei') ?>" placeholder="例：コウボ" class="<?= $appController->error($validationErrors, 'kana_sei'); ?>"></li>
										<li class="text">名</li>
										<li><input type="text" name="kana_mei" value="<?= $this->request->data('kana_mei') ?>" placeholder="例：タロウ" class="<?= $appController->error($validationErrors, 'kana_mei'); ?>"></li>
									</ul>
								</td>
							</tr>
						<? } ?>
						<? if($contest['fm_sex_disp']){ ?>
							<tr>
								<th>性別<? if($contest['fm_sex_req']){ ?><span class="required">必須</span><? } ?></th>
								<td>
									<ul class="list <?= $appController->error($validationErrors, 'sex'); ?>">
										<? foreach($config['sex'] as $key => $sex){ ?>
											<li><label for="sex<?= $key ?>"><input type="radio" name="sex" id="sex<?= $key ?>" value="<?= $key ?>"<? if($this->request->data('sex') == $key){ ?> checked="checked"<? } ?>><?= $sex ?></label></li>
										<? } ?>
									</ul>
								</td>
							</tr>
						<? } ?>
						<? if($contest['fm_age_disp']){ ?>
							<tr>
								<th>年齢<? if($contest['fm_age_req']){ ?><span class="required">必須</span><? } ?></th>
								<td><input type="text" name="age" value="<?= $this->request->data('age') ?>" placeholder="30" class="age <?= $appController->error($validationErrors, 'age'); ?>"><span>歳</span></td>
							</tr>
						<? } ?>
						<? if($contest['fm_job_disp']){ ?>
							<tr>
								<th>職業<? if($contest['fm_job_req']){ ?><span class="required">必須</span><? } ?></th>
								<td>
									<select name="job" id="business" class="<?= $appController->error($validationErrors, 'job'); ?>">
										<option value="">▼選択してください</option>
										<? foreach($config['job_apply'] as $key => $job){ ?>
											<option value="<?= $key ?>"<? if($this->request->data('job') == $key){ ?> selected="selected"<? } ?>><?= $job ?></option>
										<? } ?>
									</select>
								</td>
							</tr>
						<? } ?>
						<? if($contest['fm_mail_disp']){ ?>
							<tr>
								<th>e-mail<? if($contest['fm_mail_req']){ ?><span class="required">必須</span><? } ?></th>
								<td><input type="text" name="email" value="<?= $this->request->data('email') ?>" placeholder="例：yamada@example.com" class="<?= $appController->error($validationErrors, 'email'); ?>"></td>
							</tr>
						<? } ?>
						<? if($contest['fm_tel_disp']){ ?>
							<tr>
								<th>電話番号<? if($contest['fm_tel_req']){ ?><span class="required">必須</span><? } ?></th>
								<td>
									<ul class="tel">
										<li><input type="text" name="tel1" value="<?= $this->request->data('tel1') ?>" placeholder="例：03" class="<?= $appController->error($validationErrors, 'tel'); ?>"></li>
										<li class="text">-</li>
										<li><input type="text" name="tel2" value="<?= $this->request->data('tel2') ?>" placeholder="例：1234" class="<?= $appController->error($validationErrors, 'tel'); ?>"></li>
										<li class="text">-</li>
										<li><input type="text" name="tel3" value="<?= $this->request->data('tel3') ?>" placeholder="例：5678" class="<?= $appController->error($validationErrors, 'tel'); ?>"></li>
									</ul>
								</td>
							</tr>
						<? } ?>
						<? if($contest['fm_fax_disp']){ ?>
							<tr>
								<th>FAX番号<? if($contest['fm_fax_req']){ ?><span class="required">必須</span><? } ?></th>
								<td>
									<ul class="tel">
										<li><input type="text" name="fax1" value="<?= $this->request->data('fax1') ?>" placeholder="例：03" class="<?= $appController->error($validationErrors, 'fax'); ?>"></li>
										<li class="text">-</li>
										<li><input type="text" name="fax2" value="<?= $this->request->data('fax2') ?>" placeholder="例：1234" class="<?= $appController->error($validationErrors, 'fax'); ?>"></li>
										<li class="text">-</li>
										<li><input type="text" name="fax3" value="<?= $this->request->data('fax3') ?>" placeholder="例：5678" class="<?= $appController->error($validationErrors, 'fax'); ?>"></li>
									</ul>
								</td>
							</tr>
						<? } ?>
						<? if($contest['fm_address_disp']){ ?>
							<tr>
								<th>住所<? if($contest['fm_address_req']){ ?><span class="required">必須</span><? } ?></th>
								<td>
									<table>
										<tbody>
											<tr>
												<th>郵便番号：</th>
												<td>
													<ul class="zip">
														<li class="zip1"><input type="tel" name="zip1" value="<?= $this->request->data('zip1') ?>" maxlength="3" id="zip1" placeholder="123" class="<?= $appController->error($validationErrors, 'zip'); ?>"></li>
														<li class="text">-</li>
														<li class="zip2"><input type="tel" name="zip2" value="<?= $this->request->data('zip2') ?>" maxlength="4" id="zip2" placeholder="4567" class="<?= $appController->error($validationErrors, 'zip'); ?>"></li>
													</ul>
												</td>
											</tr>
											<tr>
												<th>都道府県：</th>
												<td>
													<select name="pref" id="pref" class="<?= $appController->error($validationErrors, 'pref'); ?>">
														<option value="">選択してください</option>
														<? foreach($config['pref'] as $key => $pref){ ?>
															<option value="<?= $key ?>"<? if($this->request->data('pref') == $key){ ?> selected="selected"<? } ?>><?= $pref ?></option>
														<? } ?>
													</select>
												</td>
											</tr>
											<tr>
												<th>市区町村以下：</th>
												<td><input type="text" name="address1" id="address" value="<?= $this->request->data('address1') ?>" placeholder="例：新宿区新宿1-2-3" class="<?= $appController->error($validationErrors, 'address1'); ?>"></td>
											</tr>
											<tr>
												<th>建物名：</th>
												<td><input type="text" name="address2" id="building" value="<?= $this->request->data('address2') ?>" placeholder="例：ABCビル101" class="<?= $appController->error($validationErrors, 'address2'); ?>"></td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						<? } ?>
						<? if($contest["fm_work_type"] == "file"){ ?>
							<tr>
								<th>応募作品<span class="required">必須</span></th>
								<td>
									<? if($contest['judge_format'] == 1){ ?>
										<p class="ate">※作品データ名は「作家名_応募作品の通し番号」にしてください。</br>例：山田太郎さんの場合→「yamadatarou_1」「yamadatarou_2」<br></p>
										<p class="ate">※合計10MBまで</p>
										<p class="ate">※アップロード可能なファイル形式は以下のとおりです。<br>

										<?php
										if(is_array($contest['accept_file_type'])){
											$accept_file_type = $contest['accept_file_type'];
										}else{
											$accept_file_type = unserialize(html_entity_decode($contest['accept_file_type']));
										}
										?>
										<? foreach($config['accept_file_type'] as $key => $value){ ?><?php if (empty($accept_file_type) || isset($accept_file_type[$value])) { ?>「.<?= $value ?>」<?php } ?><? } ?>
										</p>
									<? } ?>
									<? for($i = 1; $i <= $contest["fm_work_file_count"]; $i++){ ?>
										<div style="margin-bottom: 10px;">
<?php
if (! empty($contest["option_set_division"])){
	$division = explode("\n", $contest["division"]);
	if (! empty($division)) {
?>
											部門<?php echo $i; ?>：
                                            <select name="work_division[]" style="margin-bottom: 10px; width: calc(80% - 3em);">
<?php foreach ($division as $div) { ?>
												<option value="<?php echo $div; ?>"><?php echo $div; ?></option>
<?php } ?>
											</select><br>
											<?php
	}
}
?>
										<? if($contest['style_type'] == 0 && $contest['judge_format'] == 0){ ?>
											作品<?php echo $i; ?>：
                                            <input type="file" name="work_file[]" class="<?= $appController->error($validationErrors, 'work_contents', $i-1); ?>">
										<? }elseif($contest['judge_format'] == 1){ ?>
											<input type="file" name="work_file[]" accept="image/*" class="<?= $appController->error($validationErrors, 'work_contents', $i-1); ?>">
										<? }elseif($contest['style_type'] == 1){ ?>
											<input type="file" name="work_file[]" class="<?= $appController->error($validationErrors, 'work_contents', $i); ?>">
										<? } ?>
										</div>
<?php /* 画像ファイルの場合のみ不具合ありのため後日対応
<?php if (! empty($contest["fm_work_desc_disp"])) { ?>
										<p class="mb10">作品趣旨<?php echo $i; ?>：<br>
											<textarea name="work_desc[]" cols="30" rows="3" class="<?= $appController->error($validationErrors, 'work_desc', $i-1); ?>"><? if(isset($this->request->data['work_desc'][$i-1])){ ?><?= $this->request->data['work_desc'][$i-1] ?><? } ?></textarea>
										</p>
<?php } ?>
*/ ?>
                                        <p class="mb30"></p>
									<? } ?>
									<? if($contest['style_type'] == 1){ ?>
										<p class="ate">※写真は審査で使用しますので、作品を分かりやすく別々のアングルの写真を添付してください。</p>
									<? } ?>
									<?php if($contest['fm_work_file_type'] == "limit"){ ?>
										<p class="ate">※合計<?= WORK_FREE_FILE_SIZE ?>まで</p>
									<? }elseif($contest['style_type'] == 1){ //高岡コンペは30MB制限 20210709 Yu.Ebina Add ?>
										<p class="ate">※合計<?= WORK_TAKAOKA_FILE_SIZE ?>まで</p>
									<? }elseif($contest['judge_format'] == 1){ ?>
									<?php } else { ?>
										<p class="ate">※合計<?php echo ini_get('post_max_size'); if (preg_match('/M$/i', ini_get('post_max_size'))) echo 'B'; ?>まで</p>
									<?php } ?>
									<p class="ate">
										<? if($contest['fm_work_file_type'] != "movie" && $contest['judge_format'] != 1 ){ ?>
											※アップロード可能なファイル形式は以下のとおりです。<br>

<?php
if(is_array($contest['accept_file_type'])){
    $accept_file_type = $contest['accept_file_type'];
}else{
    $accept_file_type = unserialize(html_entity_decode($contest['accept_file_type']));
}
?>
											<? foreach($config['accept_file_type'] as $key => $value){ ?><?php if (empty($accept_file_type) || isset($accept_file_type[$value])) { ?>「.<?= $value ?>」<?php } ?><? } ?>
										<? }elseif($contest['judge_format'] == 1){ ?>
										<? }else{ ?>
											※<? foreach($config['accept_file_video'] as $key => $value){ ?>「.<?= $value ?>」<? } ?>
										<? } ?>
									</p>
								</td>
							</tr>
							<? if($contest['judge_format'] == 1){ ?>
								<tr>
									<th>注文ID<span class="required">必須</span></th>
									<td>
										<input type="text" name="order_code" value="<?= $this->request->data('order_code') ?>" class="<?= $appController->error($validationErrors, 'order_code'); ?>">
										<p class="ate">※BASE（ベイス）の注文IDを記入してください</p>
									</td>
								</tr>
							<? } ?>
						<? }elseif($contest["fm_work_type"] == "text"){ ?>
							<tr>
								<th>応募作品<span class="required">必須</span></th>
								<td>
<? for($i = 1; $i <= $contest["fm_work_text_count"]; $i++){ ?>
<?php
if (! empty($contest["option_set_division"])){
	$division = explode("\n", $contest["division"]);
	if (! empty($division)) {
?>
									部門<?php echo $i; ?>：<br>
                                    <select name="work_division[]" style="margin-bottom: 5px; width: calc(80% - 3em);">
<?php foreach ($division as $div) { ?>
										<option value="<?php echo $div; ?>"><?php echo $div; ?></option>
<? } ?>
									</select><br>
<?php
	}
}
?>
										<p class="mb10">作品<?php echo $i; ?>：<br>
											<input type="text" name="work_contents[]" value="<? if(isset($this->request->data['work_contents'][$i-1])){ ?><?= $this->request->data['work_contents'][$i-1] ?><? } ?>" class="<?= $appController->error($validationErrors, 'work_contents', $i-1); ?>">
										</p>
<?php if (! empty($contest["fm_work_kana_disp"])) { ?>
										<p class="mb10">作品ふりがな<?php echo $i; ?>：<br>
											<input type="text" name="work_kana[]" value="<? if(isset($this->request->data['work_kana'][$i-1])){ ?><?= $this->request->data['work_kana'][$i-1] ?><? } ?>" class="<?= $appController->error($validationErrors, 'work_kana', $i-1); ?>">
										</p>
<? } ?>
<?php if (! empty($contest["fm_work_desc_disp"])) { ?>
										<p class="mb10">作品趣旨<?php echo $i; ?>：<br>
											<textarea name="work_desc[]" cols="30" rows="3" class="<?= $appController->error($validationErrors, 'work_desc', $i-1); ?>"><? if(isset($this->request->data['work_desc'][$i-1])){ ?><?= $this->request->data['work_desc'][$i-1] ?><? } ?></textarea>
										</p>
<? } ?>
                                        <p class="mb40"></p>
<? } ?>
                                        <p class="ate">※1作品<?= $contest["fm_work_text_length"] ?>文字まで</p>
								</td>
							</tr>
						<? }elseif($contest["fm_work_type"] == "textbox"){ ?>
							<tr>
								<th>応募作品<span class="required">必須</span></th>
								<td>
<? for($i = 1; $i <= $contest["fm_work_textbox_count"]; $i++){ ?>
<?php
if (! empty($contest["option_set_division"])){
	$division = explode("\n", $contest["division"]);
	if (! empty($division)) {
?>
                                    <p class="mb10">
                                        部門<?php echo $i; ?>：
                                        <select name="work_division[]" style="width: calc(80% - 3em);">
<?php foreach ($division as $div) { ?>
                                            <option value="<?php echo $div; ?>"><?php echo $div; ?></option>
<? } ?>
                                        </select>
                                    </p>
<?php
	}
}
?>
                                    <p class="mb10">
                                        作品<?php echo $i; ?>：<br>
                                        <textarea name="work_contents[]" cols="30" rows="6" class="<?= $appController->error($validationErrors, 'work_contents', $i-1); ?>"><? if(isset($this->request->data['work_contents'][$i-1])){ ?><?= $this->request->data['work_contents'][$i-1] ?><? } ?></textarea>
                                    </p>
<?php if (! empty($contest["fm_work_desc_disp"])) { ?>
                                    <p class="mb10">
                                        作品趣旨<?php echo $i; ?>：<br>
                                        <textarea name="work_desc[]" cols="30" rows="3" class="<?= $appController->error($validationErrors, 'work_desc', $i-1); ?>"><? if(isset($this->request->data['work_desc'][$i-1])){ ?><?= $this->request->data['work_desc'][$i-1] ?><? } ?></textarea>
                                    </p>
<? } ?>
                                    <p class="mb50"></p>
<? } ?>
                                    <p class="ate">※1作品<?= $contest["fm_work_textbox_length"] ?>文字まで</p>
								</td>
							</tr>
                            <? } elseif ($contest["fm_work_type"] == "url") { ?>
                                <tr>
                                    <th>応募作品<span class="required">必須</span></th>
                                    <td>
                                        <?php
if (! empty($contest["option_set_division"])) {
	$division = explode("\n", $contest["division"]);
	if (! empty($division)) {
?>
                                        部門：
                                        <select name="work_division[]" style="margin-bottom: 10px; width: calc(80% - 3em);">
<?php foreach ($division as $div) { ?>
                                            <option value="<?php echo $div; ?>"><?php echo $div; ?></option>
<?php } ?>
                                        </select><br>
<?php
    }
}
?>
                                        ※リンク先URLをご記入ください。<br>
                                        <textarea name="work_contents" cols="30" rows="3" placeholder="動画配信サイト（Youtube）にアップロード後、URLをご入力ください。" class="<?= $appController->error($validationErrors, 'work_contents'); ?>"><? if(isset($this->request->data['work_contents'])){ ?><?= $this->request->data['work_contents'] ?><? } ?></textarea><br>
                                        <a href="#" target="_blank" id="url_preview">プレビュー</a>
                                    </td>
                                </tr>
						<? }elseif($contest["fm_work_type"] == "single"){ ?>
							<tr>
								<th><?= $contest["fm_work_single_name"] ?><span class="required">必須</span></th>
								<td>
									<ul class="list <?= $appController->error($validationErrors, 'work_contents'); ?>">
										<? foreach((array)explode(",", $contest["fm_work_single_values"]) as $key => $value){ ?>
											<li><label for="work_contents<?= $key+1 ?>"><input type="radio" name="work_contents[]" id="work_contents<?= $key+1 ?>" value="<?= trim($value) ?>"<? if($this->request->data('work_contents') && in_array(trim($value), (array)$this->request->data('work_contents'))){ ?> checked="checked"<? } ?>><?= trim($value) ?></label></li>
										<? } ?>
									</ul>
								</td>
							</tr>
						<? }elseif($contest["fm_work_type"] == "multi"){ ?>
							<tr>
								<th><?= $contest["fm_work_multi_name"] ?><span class="required">必須</span></th>
								<td>
									<ul class="list <?= $appController->error($validationErrors, 'work_contents'); ?>">
										<? foreach((array)explode(",", $contest["fm_work_multi_values"]) as $key => $value){ ?>
											<li><label for="work_contents<?= $key+1 ?>"><input type="checkbox" name="work_contents[]" id="work_contents<?= $key+1 ?>" value="<?= trim($value) ?>"<? if($this->request->data('work_contents') && in_array(trim($value), (array)$this->request->data('work_contents'))){ ?> checked="checked"<? } ?>><?= trim($value) ?></label></li>
										<? } ?>
									</ul>
								</td>
							</tr>
						<? } ?>
						<? if($contest['option_work_published']){ ?>
							<? if($contest['work_published'] == 2){ ?>
								<tr>
									<th>応募作品公開設定<span class="required">必須</span></th>
									<td>
										<p class="mb10">応募した作品をコンテストページに公開するかどうか、選択してください。</p>
										<ul class="list <?= $appController->error($validationErrors, 'work_published'); ?>">
											<li><label for="work_published1"><input type="radio" name="work_published" id="work_published1" value="1"<? if(strlen($this->request->data('work_published')) && $this->request->data('work_published') == 1){ ?> checked="checked"<? } ?>> 公開する</label></li>
											<li><label for="work_published0"><input type="radio" name="work_published" id="work_published0" value="0"<? if(strlen($this->request->data('work_published')) && $this->request->data('work_published') == 0){ ?> checked="checked"<? } ?>> 公開しない</label></li>
										</ul>
									</td>
								</tr>
							<? } ?>
						<? } ?>
						<? if($contest['fm_title_disp']){ ?>
							<tr>
<?php
// contest_id = 200 用の例外的な表示
if (isset($this->viewVars['contest']['id']) && $this->viewVars['contest']['id'] == 200) {
?>
								<th>題名</th>
<?php } else { ?>
								<th>作品タイトル<? if($contest['fm_title_req']){ ?><span class="required">必須</span><? } ?></th>
<?php } ?>
								<td>
									<input type="text" name="title" value="<?= $this->request->data('title') ?>" class="<?= $appController->error($validationErrors, 'title'); ?>">
								</td>
							</tr>
						<? } ?>
						<? if($contest['fm_comment_disp']){ ?>
							<tr>
<?php
// contest_id = 200 用の例外的な表示
if (isset($this->viewVars['contest']['id']) && $this->viewVars['contest']['id'] == 200) {
?>
								<th>400字詰原稿用紙換算枚数<? if($contest['fm_comment_req']){ ?><span class="required">必須</span><? } ?></th>
								<td><input type="text" name="comment" class="<?= $appController->error($validationErrors, 'comment'); ?>" value="<?= $this->request->data('comment') ?>"></td>
<?php } elseif (isset($this->viewVars['contest']['id']) && $this->viewVars['contest']['id'] == 384) { ?>
								<th>応募予定の公募<? if($contest['fm_comment_req']){ ?><span class="required">必須</span><? } ?></th>
								<td><textarea name="comment" cols="30" rows="6" class="<?= $appController->error($validationErrors, 'comment'); ?>"><?= $this->request->data('comment') ?></textarea></td>
<?php } else { ?>
								<th>作品コメント<? if($contest['fm_comment_req']){ ?><span class="required">必須</span><? } ?></th>
								<td><textarea name="comment" cols="30" rows="6" class="<?= $appController->error($validationErrors, 'comment'); ?>"><?= $this->request->data('comment') ?></textarea></td>
<?php } ?>
							</tr>
						<? } ?>
                        <? if($contest['fm_free_disp']){ ?>
                            <tr>
                                <th><?= $contest["fm_free_name"] ?><? if($contest['fm_free_req']){ ?><span class="required">必須</span><? } ?></th>
                                <td>
                                    <ul class="list <?= $appController->error($validationErrors, 'free'); ?>">
                                        <? if($contest["fm_free_type"] == "multi"){ ?>
                                            <? foreach((array)explode(",", $contest["fm_free_multi_values"]) as $key => $value){ ?>
                                                <li><label for="free<?= $key+1 ?>"><input type="checkbox" name="free[]" id="free<?= $key+1 ?>" value="<?= trim($value) ?>"<? if(in_array(trim($value), (array)$this->request->data('free'))){ ?> checked="checked"<? } ?>><?= $value ?></label></li>
                                            <? } ?>
                                        <? }elseif($contest["fm_free_type"] == "single"){ ?>
                                            <? foreach((array)explode(",", $contest["fm_free_single_values"]) as $key => $value){ ?>
                                                <li><label for="free<?= $key+1 ?>"><input type="radio" name="free" id="free<?= $key+1 ?>" value="<?= trim($value) ?>"<? if($this->request->data('free') == trim($value)){ ?> checked="checked"<? } ?>><?= $value ?></label></li>
                                            <? } ?>
                                        <? } ?>
                                    </ul>
                                </td>
                            </tr>
                        <? } ?>
                        <? if($contest['option_addfree']) { ?>
                            <? for ($i = 2; $i <= NUMBER_OF_FREE; $i++) { ?>
                                <? if($contest['fm_free'.$i.'_disp']){ ?>
                                    <tr>
                                        <th><?= $contest['fm_free'.$i.'_name'] ?><? if($contest['fm_free'.$i.'_req']){ ?><span class="required">必須</span><? } ?></th>
                                        <td>
                                            <ul class="list <?= $appController->error($validationErrors, 'free'.$i); ?>">
                                                <? if($contest['fm_free'.$i.'_type'] == 'multi'){ ?>
                                                    <? foreach((array)explode(',', $contest['fm_free'.$i.'_multi_values']) as $key => $value){ ?>
                                                        <li><label for="free<?= $i.$key+1 ?>"><input type="checkbox" name="free<?= $i ?>[]" id="free<?= $i.$key+1 ?>" value="<?= trim($value) ?>"<? if(in_array(trim($value), (array)$this->request->data('free'.$i))){ ?> checked="checked"<? } ?>><?= $value ?></label></li>
                                                    <? } ?>
                                                <? }elseif($contest['fm_free'.$i.'_type'] == 'single'){ ?>
                                                    <? foreach((array)explode(',', $contest['fm_free'.$i.'_single_values']) as $key => $value){ ?>
                                                        <li><label for="free<?= $i.$key+1 ?>"><input type="radio" name="free<?= $i ?>" id="free<?= $i.$key+1 ?>" value="<?= trim($value) ?>"<? if($this->request->data('free'.$i) == trim($value)){ ?> checked="checked"<? } ?>><?= $value ?></label></li>
                                                    <? } ?>
                                                <? } ?>
                                            </ul>
                                        </td>
                                    </tr>
                                <? } ?>
                            <? } ?>
                        <? } ?>
                        <? if($contest['option_addtext']) { ?>
                            <? for ($i = 1; $i <= NUMBER_OF_TEXT; $i++) { ?>
                                <? if($contest['fm_text'.$i.'_disp']){ ?>
                                    <tr>
                                        <th>
                                            <? if(!empty($contest['fm_text'.$i.'_name'])) {
                                                echo $contest['fm_text'.$i.'_name'];
                                            } else { ?>
                                                入力項目（1行）<? echo $i ?>
                                            <? } ?>
                                            <? if($contest['fm_text'.$i.'_req']){ ?><span class="required">必須</span><? } ?>
                                        </th>
                                        <td><input type="text" name="text<? echo $i ?>" value="<?= $this->request->data('text'.$i) ?>" class="<?= $appController->error($validationErrors, 'text'.$i); ?>"></td>
                                    </tr>
                                <? } ?>
                            <? } ?>
                            <? for ($i = 1; $i <= NUMBER_OF_TEXTAREA; $i++) { ?>
                                <? if($contest['fm_textarea'.$i.'_disp']){ ?>
                                    <tr>
                                        <th>
                                            <? if($contest['style_type'] == 1 && $contest['fm_textarea'.$i.'_name'] == "当作品の発表歴") { ?>
                                                当作品の発表歴<span class="required">必須</span>
                                            <? } elseif(!empty($contest['fm_textarea'.$i.'_name'])) {
												echo $contest['fm_textarea'.$i.'_name'];
                                            }else{ ?>
												入力項目（複数行）<? echo $i ?>
											<? } ?>
                                            <? if($contest['fm_textarea'.$i.'_req']){ ?><span class="required">必須</span><? } ?>
                                        </th>
                                        <td>
										<? if($contest['style_type'] == 1 && $contest['fm_textarea'.$i.'_name'] == "当作品の発表歴"){ ?>
											<ul class="list">
												<li><input type="radio" name="history" id="history0" value="0" <? if($this->request->data('history') == 0){ ?> checked <? } ?>><label for="history0">初めて</label></li>
												<li><input type="radio" name="history" id="history1" value="1" <? if($this->request->data('history') == 1){ ?> checked <? } ?>><label for="history1">初めてではない</label></li>
											</ul>
										<? } ?>
											<textarea name="textarea<? echo $i ?>" cols="30" rows="6" class="<?= $appController->error($validationErrors, 'textarea'.$i); ?>" <? if($contest['style_type'] == 1 && $contest['fm_textarea'.$i.'_name'] == "当作品の発表歴" && $this->request->data('history') != 1){ ?>id="history-form" placeholder="" style="display:none;"<? }elseif($contest['style_type'] == 1 && $contest['fm_textarea'.$i.'_name'] == "当作品の発表歴" && $this->request->data('history') == 1 ){ ?>id="history-form" placeholder="発表歴を入力してください"<? } ?>><?= $this->request->data('textarea'.$i) ?></textarea>
										</td>
                                    </tr>
                                <? } ?>
                            <? } ?>
                        <? } ?>
						<? if($contest['style_type'] == 1){ ?>
							<tr>
								<th>素材・用途<span class="required">必須</span></th>
								<td>
									<select name="material" id="material" class="<?= $appController->error($validationErrors, 'material'); ?>">
										<option value="">選択してください</option>
										<? foreach(Configure::read('material') as $key => $material){ ?>
											<option value="<?= $key ?>"<? if($this->request->data('material') == $key){ ?> selected="selected"<? } ?>><?= $material ?></option>
										<? } ?>
									</select>
								</td>
							</tr>
							<tr>
								<th>申請料振込日<span class="required">必須</span></th>
								<td>
									<input type="date" name="pay_date" value="<?= $this->request->data('pay_date') ?>"></input>
									<p>
										申請料5,000円を以下の口座に振り込み、振り込みを行った<br>日をご入力ください。
										<br>　口座番号：00790-0-69077
										<br>　口座名称：工芸都市高岡クラフトコンペ実行委員会
										<span class="ate">
											<br>※郵便局にて備え付けの振込用紙を使い、申請する「作家名・名前」でお振り込み下さい。
											<br>お振り込みが確認できない場合は、出品申込みは無効になります。
										</span>
									</p>
								</td>
							</tr>
						<? } ?>
						<tr>
							<th>応募規約<span class="required">必須</span></th>
							<td><textarea class="mb15 ate" rows="8" cols="50" readonly>本コンテストに応募される方（以下、「応募者」といいます）は、以下をよくお読みいただき、同意の上でご応募をお願いします。本コンテストに応募された場合には、本規約にご同頂いたものと見做しますので、ご同意頂けない場合は応募を中止して下さい。

■募集要項への同意
応募者はコンテスト主催者が別途定める募集要項に従い、本コンテストに応募することに同意します。

■利用機器、費用負担等
本サイトにアクセスするためにかかる費用は、理由の如何を問わず利用者の負担となります。

■当選・抽選
主催者は、当選者に対し、コンテストの結果を別途主催者が定める方法によりお知らせいたしますが、当選者が主催者の指定する期日までに、指定された手続きを実行されない場合、権利を失効させていただく場合があります。
また、本コンテストの賞品の発送において、応募者が真実かつ正確なデータを入力していないこと、または応募者から提供された情報が不十分であったことによって賞品が届かない場合、あるいは応募者の転居や 長期の不在などの事由によって弊社所定の期間内に応募者が賞品を受領できない場合は、当選の権利が失効・削除されることがあります。なお、当選者としての権利を第三者に譲渡 等は出来ません。

■知的財産権
本コンテストにかかわる知的財産権（著作権、商標権、意匠権、肖像権等）、その他の権利については、特に定めのない限り主催者、公募ガイド社もしくは原権利者に帰属するものとし、応募者はその権利を尊重し、無断で使用してはならないものとします。
応募者は、本コンテスト上で得られる一切の情報や画像等について、弊社もしくは原権利者の許諾を得ずに、著作権法等に定める個人の私的 使用その他法律に よって明示的に認められる範囲を超えて、これらの全部または一部の利用、転載、複製、配布、改変等をすることはできないものとします。

■不適切な行為
法令に反する行為等、主催者が不適切と判断した行為があった場合、本サービスの停止等、主催者が適切と判断した処置をとります。

■本コンテストの変更・中断・中止・終了
弊社は、本コンテストにおけるサービスの一部または全てを事前に通知することなく変更・中止・終了することができるものとします。なお、変更・中断あるいは中止または終了により生じた損害については、一切責任を負いません。

■免責
本サービスの利用は、利用者自らの判断と責任において行い、本サイトの利用によるハードウェア、ソフトウェアのトラブルや損害、通信環境または機器等の故障等によるアクセスの障害について、主催者、その代理人および公募ガイド社は責任を負いません。

■本規約の改定・変更
主催者は以上の方針を利用者に通知することなく改定・変更することがあります。

■準拠法・裁判管轄
本規約は日本法を準拠法とし、日本法に従って解釈されるものとします。また、本サービスに関連して生じた紛争は、東京地方裁判所の排他的合意管轄に属するものとします。

<? if($contest['judge_format'] == 1){ ?>
■個人情報の取扱い
ご記入いただいた個人情報は、TIS事務局にて責任をもって管理し、TISの開催する展覧会、公募のご案内以外に無断で他目的に使用することはありません。

■注意事項
・応募料は必ず応募点数分お支払いください。お支払い後（BASE決済後）の変更はできません。多すぎた場合のお支払いの返金もできません。お気をつけください。
・応募に利用するインターネット上のサービスに関連して発生する損害についても、その一切の責任を負いません。
・作品のお持込みや郵送は受け付けることができません。また送付作品の返送手続もいたしません。
・応募作品の制作に際して起こった事故、その他一切のトラブルについて、 TISは一切の責任を負わないものとします。
 <? } ?>

以上</textarea>
								<ul class="list <?= $appController->error($validationErrors, 'agreement'); ?>">
									<li><label for="agreement"><input class="error" id="agreement" name="agreement" type="checkbox"<? if($this->request->data('agreement')){ ?> checked="checked"<? } ?>>応募規約に同意する</label></li>
								</ul>
							</td>
						</tr>
					</tbody>
				</table>
<?php if (isset($contest['is_kg_contest']) && $contest['is_kg_contest'] !== false) { ?>
				<p style="text-align: center; margin: 5px 0 10px;">
					「<a href="/system/agreement.php" target="_blank">個人情報のお取扱いに関する同意事項</a>」に同意した方のみ送信してください。
				</p>
<?php } ?>
				<input type="hidden" name="token" value="<?= $this->Session->read('token') ?>">
				<input type="hidden" name="atmr_form_token" value="<?php echo $atmr_form_token; ?>">
				<p class="btn"><input class="overimg" type="submit" name='confirm' value="確認画面へ"></p>
				<?php
$limit = '2019-02-22 08:00:00';
if (date('U') <= strtotime($limit)) {
?>
				<p id="msg_title_side" style="cursor: pointer; font-weight: bold; color: #9E2424; text-align: center; font-size: 90%; margin-top: 1.5em;">※システムメンテナンスによる応募受付一時停止のお知らせ</p>
				<div id="msg_content_side" style="margin: 1em 0; padding: 1em; border: 7px solid #9E2424; background-color: #ffffff; display:none;">
					<p style="margin: 1em 0;">
						この度、システム増強に伴いメンテナンスを実施させていただくことになりました。<br>
						メンテナンスの時間帯は無料掲載申込のシステムがご利用いただけませんので、あらかじめご了承ください。<br>
					</p>
					<p style="margin: 1.5em 0;">
						<strong>■メンテナンス期間</strong><br>
						2019年02月22日（金） 04時00分 ～ 08時00分
					</p>
					<p>
						ご不便をお掛け致しますが、ご理解の程何卒よろしくお願い致します。
					</p>
				</div>
				<?php } ?>
			</form>
		</div>
	</div>
</div>