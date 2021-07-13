<?php
App::import('Model','Evaluation');
App::import('Model','ImageEvaluation');

class Apply extends AppModel {

	var $useTable = 'apply';

	public $hasMany = array(
        'Work' => array(
            'className' => 'Work',
		),
		'ImageEvaluation' => array(
            'className' => 'ImageEvaluation',
		),
		'Order' => array(
            'className' => 'Order',
        )
    );

	public function validationDetail($request, $contest, $fromAdmin = false){
		$validationErrors = array();

		if($contest['fm_penname_disp']){
			if($contest['fm_penname_req'] && !ValidationEx::notBlank($request->data('penname'))){
				if($contest['style_type'] == 0 ){
					$validationErrors['penname'] = "ペンネームが入力されていません";
				}elseif($contest['style_type'] == 1 ){
					$validationErrors['penname'] = "作家名が入力されていません";
				}
			}elseif(!ValidationEx::maxLength($request->data('penname'), TEXT_MAX_SIZE)){
				$validationErrors['penname'] = "ペンネームは".TEXT_MAX_SIZE."文字以下で入力してください";
			}
		}
		if($contest['fm_sex_disp']){
			if($contest['fm_sex_req'] && !ValidationEx::notBlank($request->data('sex'))){
				$validationErrors['sex'] = "性別が選択されていません";
			}
		}
		if($contest['fm_age_disp']){
			if($contest['fm_age_req'] && !ValidationEx::notBlank($request->data('age'))){
				$validationErrors['age'] = "年齢が入力されていません";
			}
		}
		if($contest['fm_job_disp']){
			if($contest['fm_job_req'] && !ValidationEx::notBlank($request->data('job'))){
				$validationErrors['job'] = "職業が選択されていません";
			}
		}

		if($contest['fm_free_disp']){
			if($contest['fm_free_req'] && !ValidationEx::notBlankAndArray($request->data('free'))){
				$validationErrors['free'] = $contest['fm_free_name']."が選択されていません";
			}
		}

		if($contest['option_addfree']){
			for ($i = 2; $i <= NUMBER_OF_FREE; $i++) {
				if($contest['fm_free'.$i.'_req'] && !ValidationEx::notBlankAndArray($request->data('free'.$i))){
					$validationErrors['free'.$i] = $contest['fm_free'.$i.'_name']."が選択されていません";
				}
			}
		}

		$work_contents = $request->data('work_contents');
		if($contest['fm_work_type'] == "text"){
			if(!ValidationEx::notBlank($work_contents[0])){
				if(count($work_contents) > 1){
					$validationErrors['work_contents'][0] = "応募作品1が入力されていません";
				}else{
					$validationErrors['work_contents'][0] = "応募作品が入力されていません";
				}
			}
			foreach((array)$work_contents as $key => $value){
				if(!ValidationEx::mbMaxLength($value, $contest['fm_work_text_length'])){
					$validationErrors['work_contents'][$key] = "応募作品" . ($key + 1) . "は".$contest['fm_work_text_length']."文字以下で入力してください";
				}
			}
		}elseif($contest['fm_work_type'] == "textbox"){
			if(!ValidationEx::notBlank($work_contents[0])){
				if(count($work_contents) > 1){
					$validationErrors['work_contents'][0] = "応募作品1が入力されていません";
				}else{
					$validationErrors['work_contents'][0] = "応募作品が入力されていません";
				}
			}
			foreach((array)$work_contents as $key => $value){
				if(!ValidationEx::mbMaxLength($value, $contest['fm_work_textbox_length'])){
					$validationErrors['work_contents'][$key] = "応募作品" . ($key + 1) . "は".$contest['fm_work_textbox_length']."文字以下で入力してください";
				}
			}
		}elseif($contest['fm_work_type'] == "url"){
			if (!ValidationEx::notBlank($work_contents)) {
				$validationErrors['work_contents'] = "応募作品が入力されていません";
			} elseif (!ValidationEx::between($work_contents, URL_MIN_SIZE, URL_MAX_SIZE)) {
				$validationErrors['work_contents'] = "応募作品は".URL_MIN_SIZE."文字以上、".URL_MAX_SIZE."文字以下で入力してください";
			} else if (!Validation::url($work_contents)) {
                $validationErrors['work_contents'] = "応募作品はURLを入力してください";
            }
		}elseif($contest['fm_work_type'] == "file"){
			if(isset($request->params['form'])){
				if($contest['style_type'] == 0){
					if(!$fromAdmin){
						if(!ValidationEx::notBlank($request->params['form']['work_file']['name'][0])){
							$validationErrors['work_contents'][0] = "応募作品が選択されていません";
						}
					}
				}elseif($contest['style_type'] == 1){
					if(!$fromAdmin){
						if(!ValidationEx::notBlank($request->params['form']['work_file']['name'][0]) || !ValidationEx::notBlank($request->params['form']['work_file']['name'][1]) || !ValidationEx::notBlank($request->params['form']['work_file']['name'][2]) ){
							$validationErrors['work_contents'][0] = "応募作品は必ず３枚添付してください";
						}
					}
				}
				$work_file = $request->params['form']['work_file'];

				$totalSize = 0;
				foreach($work_file['name'] as $key => $value){
					if(ValidationEx::uploadError($work_file['error'][$key])){
						if($contest['fm_work_file_type'] == "limit"){
                            $accept_file_type = Configure::read('accept_file_type');
                            $allow_type = unserialize(html_entity_decode($contest['accept_file_type']));

                            if (! empty($allow_type)) {
                                foreach ($accept_file_type as $k => $v) {
                                    if (! isset($allow_type[$v])) unset($accept_file_type[$k]);
                                }
                            }

							if(!ValidationEx::extension($work_file['name'][$key], $accept_file_type)){
								$validationErrors['work_contents'][$key] = "応募作品の拡張子は" . implode(",", $accept_file_type) . "を選択してください";
//							}elseif(!ValidationEx::mimeType($work_file['tmp_name'][$key], Configure::read('accept_mime_type'))){
//								$validationErrors['work_contents'][$key] = "応募作品の形式は" . implode(",", Configure::read('accept_file_type')) . "を選択してください";
							}
							$totalSize += $work_file['size'][$key];
						}elseif($contest['fm_work_file_type'] == "limitless"){
                            $accept_file_type = Configure::read('accept_file_type');
                            $allow_type = unserialize(html_entity_decode($contest['accept_file_type']));

                            if (! empty($allow_type)) {
                                foreach ($accept_file_type as $k => $v) {
                                    if (! isset($allow_type[$v])) unset($accept_file_type[$k]);
                                }
                            }
                            if(!ValidationEx::extension($work_file['name'][$key], $accept_file_type)){
								$validationErrors['work_contents'][$key] = "応募作品の拡張子は" . implode(",", $accept_file_type) . "を選択してください";
//							}elseif(!ValidationEx::mimeType($work_file['tmp_name'][$key], Configure::read('accept_mime_type'))){
//								$validationErrors['work_contents'][$key] = "応募作品の形式は" . implode(",", Configure::read('accept_file_type')) . "を選択してください";
							}

							//高岡コンペを選択している場合のみ有料会員でも制限がつくためファイルサイズを計算する。 20210709 Yu.Ebina Add
							if($contest['style_type'] == 1){
								$totalSize += $work_file['size'][$key];
							}
						}elseif($contest['fm_work_file_type'] == "movie"){
							if(!ValidationEx::extension($work_file['name'][$key], Configure::read('accept_file_video'))){
								$validationErrors['work_contents'][$key] = "応募作品の拡張子は" . implode(",", Configure::read('accept_file_video')) . "を選択してください";
//							}elseif(!ValidationEx::mimeType($work_file['tmp_name'][$key], Configure::read('accept_mime_video'))){
//								$validationErrors['work_contents'][$key] = "応募作品の形式は" . implode(",", Configure::read('accept_file_video')) . "を選択してください";
							}
						}
					}
				}
				if($contest['fm_work_file_type'] == "limit"){
					if(!ValidationEx::fileSizeEx($totalSize, "<=", WORK_FREE_FILE_SIZE)){
						$validationErrors['work_contents'][0] = "応募作品のサイズは合計".WORK_FREE_FILE_SIZE."以下を選択してください";
					}
				}
				//高岡コンペを選択している場合のみファイル制限を合計30MBまでに制限する。 20210709 Yu.Ebina Add
				if($contest['style_type'] == 1){
					//if(!$fromAdmin){
						if(!ValidationEx::fileSizeEx($totalSize, "<=", WORK_TAKAOKA_FILE_SIZE)){
							$validationErrors['work_contents'][0] = "応募作品のサイズは合計".WORK_TAKAOKA_FILE_SIZE."以下を選択してください";
						}
					//}
				}
			}
		}elseif($contest['fm_work_type'] == "single"){
			if(!ValidationEx::notBlankAndArray($work_contents)){
				$validationErrors['work_contents'] = $contest["fm_work_single_name"] . "が選択されていません";
			}
		}elseif($contest['fm_work_type'] == "multi"){
			if(!ValidationEx::notBlankAndArray($work_contents)){
				$validationErrors['work_contents'] = $contest["fm_work_multi_name"] . "が選択されていません";
			}
		}

		if($contest['option_work_published']){
			if($contest['work_published'] == 2){
				if(!ValidationEx::notBlank($request->data('work_published'))){
					$validationErrors['work_published'] = "応募作品公開設定が選択されていません";
				}
			}
		}

        if ($contest['fm_work_type'] == "text" && ! empty($contest['fm_work_kana_disp'])) {
            $work_kana = $request->data('work_kana');

            foreach((array)$work_kana as $key => $value){
                if(!ValidationEx::mbMaxLength($value, ($contest['fm_work_text_length'] * 2) )){
                    $validationErrors['work_kana'][$key] = "応募作品(ふりがな)" . ($key + 1) . "は" . ($contest['fm_work_text_length'] * 2) . "文字以下で入力してください";
                }
            }

            if (! empty($contest['fm_work_kana_req'])) {
                if (!ValidationEx::notBlank($work_kana[0])) {
                    if(count($work_kana) > 1){
                        $validationErrors['work_kana'][0] = "応募作品1(ふりがな)が入力されていません";
                    }else{
                        $validationErrors['work_kana'][0] = "応募作品(ふりがな)が入力されていません";
                    }
                    if(!preg_match('/[^ぁ-んー]/u',$value)){
                        $validationErrors['work_kana'][$key] = "応募作品(ふりがな)" . ($key + 1) . "はすべてひらがなで入力してください";
                    }
                }

                $work_contents = $request->data('work_contents');
                foreach((array)$work_contents as $key => $value){
                    if ($key >= 1 && ! empty($value) && empty($work_kana[$key])) {
                        $validationErrors['work_kana'][$key] = "応募作品" . ($key + 1) . "(ふりがな)が入力されていません";
                    }
                    if ($key >= 1 && empty($value) && ! empty($work_kana[$key])) {
                        $validationErrors['work_contents'][$key] = "応募作品" . ($key + 1) . "が入力されていません";
                    }
                }
            }
        }

        if ($contest['fm_work_desc_disp'] && ($contest['fm_work_type'] == "text" || $contest['fm_work_type'] == "textbox")) {
            $work_desc = $request->data('work_desc');
            if ($contest['fm_work_desc_req']) {
                if (!ValidationEx::notBlank($work_desc[0])) {
                    if (count($work_desc) > 1) {
                        $validationErrors['work_desc'][0] = "応募作品趣旨1が入力されていません";
                    } else {
                        $validationErrors['work_desc'][0] = "応募作品趣旨が入力されていません";
                    }
                }
			}
            foreach ((array)$work_desc as $key => $value) {
                if (!ValidationEx::maxLength($value, TEXTAREA_MAX_SIZE)) {
                    if (count($work_desc) > 1) {
                        $validationErrors['work_desc'][$key] = "応募作品趣旨".$key."は".TEXTAREA_MAX_SIZE."文字以下で入力してください";
                    } else {
                        $validationErrors['work_desc'][$key] = "応募作品趣旨は".TEXTAREA_MAX_SIZE."文字以下で入力してください";
                    }
                }
            }
        }

        if($contest['fm_title_disp']){
			if($contest['fm_title_req'] && !ValidationEx::notBlank($request->data('title'))){
				$validationErrors['title'] = "作品タイトルが入力されていません";
			}elseif(!ValidationEx::maxLength($request->data('title'), TEXT_MAX_SIZE)){
				$validationErrors['title'] = "作品タイトルは".TEXT_MAX_SIZE."文字以下で入力してください";
			}
		}
		if($contest['fm_comment_disp']){
			if($contest['fm_comment_req'] && !ValidationEx::notBlank($request->data('comment'))){
				$validationErrors['comment'] = "作品コメントが入力されていません";
			}elseif(!ValidationEx::maxLength($request->data('comment'), TEXTAREA_MAX_SIZE)){
				$validationErrors['comment'] = "作品コメントは".TEXTAREA_MAX_SIZE."文字以下で入力してください";
			}
		}
		if($contest['option_addtext']){
    	    for ($i = 1; $i <= NUMBER_OF_TEXT; $i++) {
    	        if ($contest['fm_text'.$i.'_disp']) {
    	            $fieldName = "入力項目（1行）".$i;
    	            if (!empty($contest['fm_text'.$i.'_name'])) $fieldName = $contest['fm_text'.$i.'_name'];
    	            if ($contest['fm_text'.$i.'_req'] && !ValidationEx::notBlank($request->data('text'.$i))) {
    	                $validationErrors['text'.$i] = $fieldName."が入力されていません";
    	            } elseif (!ValidationEx::maxLength($request->data('text'.$i), TEXT_MAX_SIZE)) {
    				    $validationErrors['text'.$i] = $fieldName."は".TEXT_MAX_SIZE."文字以下で入力してください";
    			    }
    	        }
    		}
            for ($i = 1; $i <= NUMBER_OF_TEXTAREA; $i++) {
                if ($contest['fm_textarea'.$i.'_disp']) {
                    $fieldName = "入力項目（複数行）".$i;
                    if (!empty($contest['fm_textarea'.$i.'_name'])) $fieldName = $contest['fm_textarea'.$i.'_name'];
                    if ($contest['fm_textarea'.$i.'_req'] && !ValidationEx::notBlank($request->data('textarea'.$i))) {
                        $validationErrors['textarea'.$i] = $fieldName."が入力されていません";
                    } elseif (!ValidationEx::maxLength($request->data('textarea'.$i), TEXTAREA_MAX_SIZE)) {
                        $validationErrors['textarea'.$i] = $fieldName."は".TEXTAREA_MAX_SIZE."文字以下で入力してください";
                    }elseif ($request->data('history') == 1 && $contest['fm_textarea'.$i.'_name'] == "当作品の発表歴" && !ValidationEx::notBlank($request->data('textarea'.$i))) {
                    	$validationErrors['textarea'.$i] = $fieldName."を入力してください";
                    }
                }
            }
        }
		if($contest['fm_name_disp']){
			if($contest['fm_name_req'] && !ValidationEx::notBlank($request->data('name_sei'))){
				$validationErrors['name_sei'] = "お名前 性が入力されていません";
			}elseif(!ValidationEx::maxLength($request->data('name_sei'), TEXT_MAX_SIZE)){
				$validationErrors['name_sei'] = "お名前 性は".TEXT_MAX_SIZE."文字以下で入力してください";
			}
			if($contest['fm_name_req'] && !ValidationEx::notBlank($request->data('name_mei'))){
				$validationErrors['name_mei'] = "お名前 名が入力されていません";
			}elseif(!ValidationEx::maxLength($request->data('name_mei'), TEXT_MAX_SIZE)){
				$validationErrors['name_mei'] = "お名前 名は".TEXT_MAX_SIZE."文字以下で入力してください";
			}
		}
		if($contest['fm_kana_disp']){
			if($contest['fm_kana_req'] && !ValidationEx::notBlank($request->data('kana_sei'))){
				$validationErrors['kana_sei'] = "お名前（カナ）性が入力されていません";
			}elseif(!ValidationEx::maxLength($request->data('kana_sei'), TEXT_MAX_SIZE)){
				$validationErrors['kana_sei'] = "お名前（カナ）性は".TEXT_MAX_SIZE."文字以下で入力してください";
			}elseif(!ValidationEx::onlyKatakana($request->data('kana_sei'), TEXT_MAX_SIZE)){
				$validationErrors['kana_sei'] = "お名前（カナ）性はカタカナで入力してください";
			}
			if($contest['fm_kana_req'] && !ValidationEx::notBlank($request->data('kana_mei'))){
				$validationErrors['kana_mei'] = "お名前（カナ）名が入力されていません";
			}elseif(!ValidationEx::maxLength($request->data('kana_mei'), TEXT_MAX_SIZE)){
				$validationErrors['kana_mei'] = "お名前（カナ）名は".TEXT_MAX_SIZE."文字以下で入力してください";
			}elseif(!ValidationEx::onlyKatakana($request->data('kana_mei'), TEXT_MAX_SIZE)){
				$validationErrors['kana_mei'] = "お名前（カナ）名はカタカナで入力してください";
			}
		}
		if($contest['fm_mail_disp']){
			if($contest['fm_mail_req'] && !ValidationEx::notBlank($request->data('email'))){
				$validationErrors['email'] = "メールアドレスが入力されていません";
			}elseif(ValidationEx::notBlank($request->data('email')) && !ValidationEx::email($request->data('email'))){
				$validationErrors['email'] = "メールアドレスの形式が正しくありません";
			}
		}

		if($contest['fm_tel_disp']){
			if($contest['fm_tel_req'] && !ValidationEx::notBlank($request->data('tel1'))){
				$validationErrors['tel'] = "電話番号が入力されていません";
			}elseif(!ValidationEx::telNo($request->data('tel1'), $request->data('tel2'), $request->data('tel3'))){
				$validationErrors['tel'] = "電話番号を正しく入力してください";
			}
		}
		if($contest['fm_fax_disp']){
			if($contest['fm_fax_req'] && !ValidationEx::notBlank($request->data('fax1'))){
				$validationErrors['fax'] = "FAX番号が入力されていません";
			}elseif(!ValidationEx::telNo($request->data('fax1'), $request->data('fax2'), $request->data('fax3'))){
				$validationErrors['fax'] = "FAX番号を正しく入力してください";
			}
		}

		if($contest['fm_address_disp']){
			if($contest['fm_address_req'] && !ValidationEx::notBlank($request->data('pref'))){
				$validationErrors['pref'] = "都道府県が選択されていません";
			}
			if($contest['fm_address_req'] && (!ValidationEx::notBlank($request->data('zip1')) || !ValidationEx::notBlank($request->data('zip2')))){
				$validationErrors['zip'] = "郵便番号が入力されていません";
			}elseif(!ValidationEx::zipCode($request->data('zip1'), $request->data('zip2'))){
				$validationErrors['zip'] = "郵便番号を正しく入力してください";
			}
			if($contest['fm_address_req'] && !ValidationEx::notBlank($request->data('address1'))){
				$validationErrors['address1'] = "市区町村以下が入力されていません";
			}elseif(!ValidationEx::maxLength($request->data('address1'), TEXT_MAX_SIZE)){
				$validationErrors['address1'] = "市区町村以下は".TEXT_MAX_SIZE."文字以下で入力してください";
			}

			if(!ValidationEx::maxLength($request->data('address2'), TEXT_MAX_SIZE)){
				$validationErrors['address2'] = "建物名は".TEXT_MAX_SIZE."文字以下で入力してください";
			}
		}
		if($contest['style_type'] == 1 && !ValidationEx::notBlank($request->data('material'))){
			$validationErrors['material'] = "素材・用途が選択されていません";
		}
		if($contest['style_type'] == 1 && !ValidationEx::notBlank($request->data('pay_date'))){
			$validationErrors['pay_date'] = "申請料振込日が入力されていません";
		}

		if(!$fromAdmin){
			if(!ValidationEx::notBlank($request->data('agreement'))){
				$validationErrors['agreement'] = "応募規約が選択されていません";
			}
		}
		if($contest['judge_format'] == 1 && $request->data('order_code')){
			$order = ClassRegistry::init('Order');
			$order_item = $order->getOrderData($request->data('order_code'));
			$workFile = array_filter($request->params['form']['work_file']['name']);
			list($order_item_data, $order_count) = $order->findOrderItem($request->data('order_code'));
			if (!ValidationEx::notBlank($request->data('order_code'))) {
				$validationErrors['order_code'] = "注文IDを入力してください";
			} elseif ($order_item == false) {
				$validationErrors['order_code'] = "この注文IDは既に登録されています";
			} elseif ($order_item == true && $order_item_data == false) {
                $validationErrors['order_code'] = "注文IDが存在しません";
            }elseif($order_count < count($workFile)){
				$validationErrors['order_code'] = "応募作品が入金口数より多すぎます";
			}
		}

		return $validationErrors;
	}

	public function getApply($id, $sponsor_id = ""){

		$conditions = array('Apply.id' => $id);
		if($sponsor_id){
			$conditions['Contest.sponsor_id'] = $sponsor_id;
		}

		$joins[] = array(
			'type' => 'inner',
			'table' => 'contest',
			'alias' => 'Contest',
			'conditions' => 'Apply.contest_id = Contest.id',
		);

		$data = $this->find('first', array(
			'conditions' => $conditions,
			'joins' => $joins,
		));

		return $data;
	}

	public function getMaterial($key){
		$material = Set::enum($key, '"",金属,漆,木工,陶磁器,ガラス,ジュエリー,テキスタイル,家具,照明,その他');
		return $material;
	}

	public function getApplyList($searchCond, $useJoin = true){
		$conditions = array();
		$order = array();

		if($useJoin){
			$joins[] = array(
				'type' => 'inner',
				'table' => 'work',
				'alias' => 'Work',
				'conditions' => 'Apply.id = Work.apply_id',
			);
		}else{
			$joins = array();
		}

		if(isset($searchCond['contest_id']) && $searchCond['contest_id']){
			$conditions['Apply.contest_id'] = $searchCond['contest_id'];
		}
        if( isset($searchCond['term_start']) && preg_match ('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/i', $searchCond['term_start']) ){
            $conditions['Apply.cdate >='] = $searchCond['term_start'] . ' : 00:00:00';
        }
        if( isset($searchCond['term_end']) && preg_match ('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/i', $searchCond['term_end']) ){
            $conditions['Apply.cdate <='] = $searchCond['term_end'] . ' : 23:59:59';
        }
		if(isset($searchCond['penname']) && $searchCond['penname']){
			$conditions['Apply.penname LIKE'] = "%".$searchCond['penname']."%";
		}
        if(isset($searchCond['name_sei']) && $searchCond['name_sei']){
            $conditions['Apply.name_sei LIKE'] = "%".$searchCond['name_sei']."%";
        }
        if(isset($searchCond['name_mei']) && $searchCond['name_mei']){
            $conditions['Apply.name_mei LIKE'] = "%".$searchCond['name_mei']."%";
        }
		if(isset($searchCond['kana_sei']) && $searchCond['kana_sei']){
            $conditions['Apply.kana_sei LIKE'] = "%".$searchCond['kana_sei']."%";
        }
        if(isset($searchCond['kana_mei']) && $searchCond['kana_mei']){
            $conditions['Apply.kana_mei LIKE'] = "%".$searchCond['kana_mei']."%";
        }
        if(isset($searchCond['tel1']) && $searchCond['tel1']){
            $conditions['Apply.tel1 LIKE'] = "%".$searchCond['tel1']."%";
        }
        if(isset($searchCond['tel2']) && $searchCond['tel2']){
            $conditions['Apply.tel2 LIKE'] = "%".$searchCond['tel2']."%";
        }
        if(isset($searchCond['tel3']) && $searchCond['tel3']){
            $conditions['Apply.tel3 LIKE'] = "%".$searchCond['tel3']."%";
        }
        if(isset($searchCond['email']) && $searchCond['email']){
            $conditions['Apply.email LIKE'] = "%".$searchCond['email']."%";
        }
		if(isset($searchCond['division']) && $searchCond['division']){
			$conditions['Work.work_division'] = $searchCond['division'];
		}
		if(isset($searchCond['title']) && $searchCond['title']){
			$conditions['Apply.title LIKE'] = "%".$searchCond['title']."%";
		}
		if(isset($searchCond['pref']) && $searchCond['pref']){
			$conditions['Apply.pref'] = $searchCond['pref'];
		}
		if(isset($searchCond['age_from']) && $searchCond['age_from']){
			$conditions['Apply.age >='] = $searchCond['age_from'];
		}
		if(isset($searchCond['age_to']) && $searchCond['age_to']){
			$conditions['Apply.age <='] = $searchCond['age_to'];
		}
		if(isset($searchCond['sex']) && $searchCond['sex']){
			$conditions['Apply.sex'] = $searchCond['sex'];
		}
		if(isset($searchCond['sex']) && $searchCond['material']){
			$conditions['Apply.material'] = $searchCond['material'];
		}
		if(isset($searchCond['work_published']) && $searchCond['work_published']){
			$conditions['Apply.work_published'] = $searchCond['work_published'];
		}
		if(isset($searchCond['work_no'])){
			$conditions['Work.work_no'] = $searchCond['work_no'];
		}
		if(isset($searchCond['forRank']) && $searchCond['forRank']){
			$conditions['Work.vote_count >='] = 1;
		}

		if(isset($searchCond['is_adoption']) && $searchCond['is_adoption']){
			$conditions['Work.is_adoption'] = $searchCond['is_adoption'];
		}
		if(isset($searchCond['has_email']) && $searchCond['has_email']){
			$conditions[] =  array('not' => array('Apply.email' => ""));
		}
		$evaluationWorkList = array();
		if(isset($searchCond['no_evaluation']) && $searchCond['no_evaluation']){
			$work = ClassRegistry::init('Work');
			$searchWork['contest_id'] = $searchCond['contest_id'];
			if(isset($searchCond['judge_evaluation']) && $searchCond['judge_evaluation']){
				$searchWork['judge_sponsor_id'] = $searchCond['judge_evaluation'];
			}
			$evaluationList = $work->getNoEvaluation($searchWork);
			foreach($evaluationList as $key => $value){
				$evaluationWorkList[] = $value['Work']['id'];
			}
		}
		if(isset($searchCond['evaluation']) && $searchCond['evaluation']){
			$evaluation = new Evaluation;
			$searchEvaluation['contest_id'] = $searchCond['contest_id'];
			$searchEvaluation['evaluation'] = $searchCond['evaluation'];
			if(isset($searchCond['judge_evaluation']) && $searchCond['judge_evaluation']){
				$searchEvaluation['judge_sponsor_id'] = $searchCond['judge_evaluation'];
			}
			$evaluationList = $evaluation->getEvaluationListForSearch($searchEvaluation);
			foreach($evaluationList as $key => $value){
				$evaluationWorkList[] = $value['Evaluation']['work_id'];
			}
		}
		if(isset($searchCond['judge']) && $searchCond['judge']){
			$evaluation = new Evaluation;
			$searchEvaluation['contest_id'] = $searchCond['contest_id'];
			$searchEvaluation['judge_sponsor_id'] = $searchCond['judge'];
			$evaluationList = $evaluation->getEvaluationListForSearch($searchEvaluation);
			foreach($evaluationList as $key => $value){
				$evaluationWorkList[] = $value['Evaluation']['work_id'];
			}
		}
		if(count($evaluationWorkList) != 0){
			$conditions['Work.id'] = $evaluationWorkList;
		}

		if(isset($searchCond['order'])){
			if($searchCond['order'] == "cdateAsc"){
				$order = array("Apply.cdate ASC");
			}elseif($searchCond['order'] == "cdateDesc"){
				$order = array("Apply.cdate DESC");
			}elseif($searchCond['order'] == "titleAsc"){
				$order = array("Apply.title ASC");
			}elseif($searchCond['order'] == "titleDesc"){
				$order = array("Apply.title DESC");
			}elseif($searchCond['order'] == "nameAsc"){
				$order = array("Apply.penname ASC");
			}elseif($searchCond['order'] == "nameDesc"){
				$order = array("Apply.penname DESC");
			}elseif($searchCond['order'] == "adoOrder"){
				$order = array("Work.ado_order ASC");
			}elseif($searchCond['order'] == "rankOrder"){
				$order = array("Work.vote_count DESC", "Apply.cdate DESC");
			}elseif($searchCond['order'] == "evaluationSumDesc"){
				$order = array("Work.evaluation_sum DESC", "Apply.cdate DESC");
			}elseif($searchCond['order'] == "evaluationSumAsc"){
				$order = array("Work.evaluation_sum ASC", "Apply.cdate ASC");
			}elseif($searchCond['order'] == "evaluationAvgDesc"){
				$order = array("Work.evaluation_avg DESC", "Apply.cdate DESC");
			}elseif($searchCond['order'] == "evaluationAvgAsc"){
				$order = array("Work.evaluation_avg ASC", "Apply.cdate ASC");
			}else{
				$order = array("Apply.cdate DESC");
			}
		}else{
			$order = array("Apply.cdate DESC");
		}

		if(!isset($searchCond['page'])){
			$page = 0;
		}else{
			$page = $searchCond['page'];
		}
		if(!isset($searchCond['limit'])){
			$limit = 0;
		}else{
			$limit = $searchCond['limit'];
		}
		$offset = ($page - 1) * $limit;

		$data = $this->find('all', array(
			'conditions' => $conditions,
			'order' => $order,
			'limit' => $limit,
			'offset' => $offset,
			'joins' => $joins,
			'fields' => '*',
		));

		$count = $this->find('count', array(
			'conditions' => $conditions,
			'joins' => $joins,
		));

		return array($data, $count);
	}


	public function getApplyimageList($searchCond, $useJoin = true){
		$conditions = array();
		$order = array();

		if($useJoin){
			$joins[] = array(
				'type' => 'inner',
				'table' => 'work',
				'alias' => 'Work',
				'conditions' => 'Apply.id = Work.apply_id',
			);
		}else{
			$joins = array();
		}

		if(isset($searchCond['contest_id']) && $searchCond['contest_id']){
			$conditions['Apply.contest_id'] = $searchCond['contest_id'];
		}
		if(isset($searchCond['receipt_number_first']) && $searchCond['receipt_number_first']){
			if(isset($searchCond['receipt_number_last']) && $searchCond['receipt_number_last']){
				$conditions['Apply.receipt_number BETWEEN ? AND ?'] = array($searchCond['receipt_number_first'], $searchCond['receipt_number_last']);
			}
		}
		if(isset($searchCond['contest_id']) && $searchCond['contest_id']){
			$conditions['Apply.contest_id'] = $searchCond['contest_id'];
		}
        if( isset($searchCond['term_start']) && preg_match ('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/i', $searchCond['term_start']) ){
            $conditions['Apply.cdate >='] = $searchCond['term_start'] . ' : 00:00:00';
        }
        if( isset($searchCond['term_end']) && preg_match ('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/i', $searchCond['term_end']) ){
            $conditions['Apply.cdate <='] = $searchCond['term_end'] . ' : 23:59:59';
        }
		if(isset($searchCond['penname']) && $searchCond['penname']){
			$conditions['Apply.penname LIKE'] = "%".$searchCond['penname']."%";
		}
        if(isset($searchCond['name_sei']) && $searchCond['name_sei']){
            $conditions['Apply.name_sei LIKE'] = "%".$searchCond['name_sei']."%";
        }
        if(isset($searchCond['name_mei']) && $searchCond['name_mei']){
            $conditions['Apply.name_mei LIKE'] = "%".$searchCond['name_mei']."%";
        }
		if(isset($searchCond['kana_sei']) && $searchCond['kana_sei']){
            $conditions['Apply.kana_sei LIKE'] = "%".$searchCond['kana_sei']."%";
        }
        if(isset($searchCond['kana_mei']) && $searchCond['kana_mei']){
            $conditions['Apply.kana_mei LIKE'] = "%".$searchCond['kana_mei']."%";
        }
        if(isset($searchCond['tel1']) && $searchCond['tel1']){
            $conditions['Apply.tel1 LIKE'] = "%".$searchCond['tel1']."%";
        }
        if(isset($searchCond['tel2']) && $searchCond['tel2']){
            $conditions['Apply.tel2 LIKE'] = "%".$searchCond['tel2']."%";
        }
        if(isset($searchCond['tel3']) && $searchCond['tel3']){
            $conditions['Apply.tel3 LIKE'] = "%".$searchCond['tel3']."%";
        }
        if(isset($searchCond['email']) && $searchCond['email']){
            $conditions['Apply.email LIKE'] = "%".$searchCond['email']."%";
        }
		if(isset($searchCond['division']) && $searchCond['division']){
			$conditions['Work.work_division'] = $searchCond['division'];
		}
		if(isset($searchCond['title']) && $searchCond['title']){
			$conditions['Apply.title LIKE'] = "%".$searchCond['title']."%";
		}
		if(isset($searchCond['pref']) && $searchCond['pref']){
			$conditions['Apply.pref'] = $searchCond['pref'];
		}
		if(isset($searchCond['age_from']) && $searchCond['age_from']){
			$conditions['Apply.age >='] = $searchCond['age_from'];
		}
		if(isset($searchCond['age_to']) && $searchCond['age_to']){
			$conditions['Apply.age <='] = $searchCond['age_to'];
		}
		if(isset($searchCond['sex']) && $searchCond['sex']){
			$conditions['Apply.sex'] = $searchCond['sex'];
		}
		if(isset($searchCond['work_published']) && $searchCond['work_published']){
			$conditions['Apply.work_published'] = $searchCond['work_published'];
		}
		if(isset($searchCond['is_adoption']) && $searchCond['is_adoption']){
			$conditions['Work.is_adoption'] = $searchCond['is_adoption'];
		}
		if(isset($searchCond['is_diff']) && $searchCond['is_diff']){
			$conditions[] = array(
						'OR' => array(
							'Order.quantity != countWork',
							'Order.quantity is null',
						));
		}
		if(isset($searchCond['has_email']) && $searchCond['has_email']){
			$conditions[] =  array('not' => array('Apply.email' => ""));
		}
		$evaluationWorkList = array();
		if(isset($searchCond['no_evaluation']) && $searchCond['no_evaluation']){
			$work = ClassRegistry::init('Work');
			$searchWork['contest_id'] = $searchCond['contest_id'];
			if(isset($searchCond['judge_evaluation']) && $searchCond['judge_evaluation']){
				$searchWork['judge_sponsor_id'] = $searchCond['judge_evaluation'];
			}
			$evaluationList = $work->getNoEvaluation($searchWork);
			foreach($evaluationList as $key => $value){
				$evaluationWorkList[] = $value['Work']['id'];
			}
		}
		if(isset($searchCond['evaluation']) && $searchCond['evaluation']){
			$evaluation = new Evaluation;
			$searchEvaluation['contest_id'] = $searchCond['contest_id'];
			$searchEvaluation['evaluation'] = $searchCond['evaluation'];
			if(isset($searchCond['judge_evaluation']) && $searchCond['judge_evaluation']){
				$searchEvaluation['judge_sponsor_id'] = $searchCond['judge_evaluation'];
			}
			$evaluationList = $evaluation->getEvaluationListForSearch($searchEvaluation);
			foreach($evaluationList as $key => $value){
				$evaluationWorkList[] = $value['Evaluation']['work_id'];
			}
		}
		if(isset($searchCond['judge']) && $searchCond['judge']){
			$evaluation = new Evaluation;
			$searchEvaluation['contest_id'] = $searchCond['contest_id'];
			$searchEvaluation['judge_sponsor_id'] = $searchCond['judge'];
			$evaluationList = $evaluation->getEvaluationListForSearch($searchEvaluation);
			foreach($evaluationList as $key => $value){
				$evaluationWorkList[] = $value['Evaluation']['work_id'];
			}
		}
		// 未審査の検索
		if(isset($searchCond['judged']) && $searchCond['judged']){
			$evaluation = new ImageEvaluation;
			$searchEvaluation['contest_id'] = $searchCond['contest_id'];
			if(isset($searchCond['judge_evaluation']) && $searchCond['judge_evaluation']){
				$searchEvaluation['judge_sponsor_id'] = $searchCond['judge_evaluation'];
			}
			if(in_array(0 , $searchCond['judged']) && !in_array(1 , $searchCond['judged'])){
				$searchEvaluation['evaluation_flg'] = array(1, 2);
				$evaluationList = $evaluation->getImageEvaluationListForSearch($searchEvaluation);
				$judgedEvaluationApplyList = [];
				foreach($evaluationList as $key => $value){
					$judgedEvaluationApplyList[] = $value['ImageEvaluation']['apply_id'];
				}
				$allApply = $this->find('all',array('conditions' => array('contest_id' => $searchEvaluation['contest_id'])));
				foreach($allApply as $apply){
					$allApplyId[] = $apply['Apply']['id'];
				}
				// 全ての応募から審査済のものを除外する。
				$noJudgeApply = array_diff($allApplyId, $judgedEvaluationApplyList);
				$conditions['Apply.id'] = $noJudgeApply;
			}
		}
		// 通過・不通過の検索
		if(isset($searchCond['image_evaluation']) && $searchCond['image_evaluation']){
			$evaluation = new ImageEvaluation;
			$searchEvaluation['contest_id'] = $searchCond['contest_id'];
			$searchEvaluation['evaluation_flg'] = $searchCond['image_evaluation'];
			if(isset($searchCond['judge_evaluation']) && $searchCond['judge_evaluation']){
				$searchEvaluation['judge_sponsor_id'] = $searchCond['judge_evaluation'];
			}
			$evaluationList = $evaluation->getImageEvaluationListForSearch($searchEvaluation);
			foreach($evaluationList as $key => $value){
				$evaluationApplyList[] = $value['ImageEvaluation']['apply_id'];
			}
			if(isset($judgedEvaluationApplyList) && isset($noJudgeApply)){
				// 通過・不通過・未審査が選択されたら全ての作品を表示
				$evaluationApply = array_intersect($judgedEvaluationApplyList, $evaluationApplyList);
				$conditions['Apply.id'] = array_merge($evaluationApply, $noJudgeApply);
			}elseif(isset($judgedEvaluationApplyList)){
				$conditions['Apply.id'] = array_intersect($judgedEvaluationApplyList, $evaluationApplyList);
			}else{
				$conditions['Apply.id'] = $evaluationApplyList;
			}
		}
		$work_conditions = [];
		if(count($evaluationWorkList) != 0){
			$work_conditions['Work.id'] = $evaluationWorkList;
		}

		if(isset($searchCond['order'])){
			if($searchCond['order'] == "receiptNumberAsc"){
				$order = array("Apply.receipt_number ASC");
			}elseif($searchCond['order'] == "receiptNumberDesc"){
				$order = array("Apply.receipt_number DESC");
			}elseif($searchCond['order'] == "countEvaluationAsc"){
				$order = array("countEvaluation ASC");
			}elseif($searchCond['order'] == "countEvaluationDesc"){
				$order = array("countEvaluation DESC");
			}
		}

		$order_group = 'Order.id';
		$order_db = $this->Order->getDataSource();
		$order_subQuery = $order_db->buildStatement(
			array(
				'fields'     => array('apply_id','quantity'),
				'table'      => $order_db->fullTableName($this->Order),
				'alias'      => 'Order',
				'group'      => $order_group,
			),
			$this->Order
		);

		// workテーブルのサブクエリ
		$work_group = 'Work.apply_id';
		$work_db = $this->Work->getDataSource();
		$work_subQuery = $work_db->buildStatement(
			array(
				'conditions' => $work_conditions,
				'fields'     => array('apply_id, count(apply_id) as countWork, is_adoption'),
				'table'      => $work_db->fullTableName($this->Work),
				'alias'      => 'Work',
				'group'      => $work_group,
			),
			$this->Work
		);

		if(!isset($searchCond['page'])){
			$page = 0;
		}else{
			$page = $searchCond['page'];
		}
		if(!isset($searchCond['limit'])){
			$limit = 0;
		}else{
			$limit = $searchCond['limit'];
		}
		$offset = ($page - 1) * $limit;

		$joins = array(
			array(
				'table' => 'image_evaluation',
				'alias' => 'ImageEvaluation',
				'type' => 'left',
				'conditions' => array(
					'Apply.id = ImageEvaluation.apply_id',
					'ImageEvaluation.evaluation_flg = 1',
					'ImageEvaluation.commit_flg = 1',
				)
			),
			array(
			'table' =>  "({$order_subQuery})",
			'alias' => 'Order',
			'type' => 'left',
			'conditions' => array(
				'Apply.id = Order.apply_id',
				)
			),
			array(
			'table' =>  "({$work_subQuery})",
			'alias' => 'Work',
			'type' => 'left',
			'conditions' => array(
				'Apply.id = Work.apply_id',
				)
			)
		);
		$group = 'Apply.id';
		$fields = '*, count(evaluation_flg) as countEvaluation';
		$data = $this->find('all', array(
			'conditions' => $conditions,
			'order' => $order,
			'limit' => $limit,
			'offset' => $offset,
			'joins' => $joins,
			'group' => $group,
			'fields' => $fields,
		));
		$count = $this->find('all', array(
			'conditions' => $conditions,
			'joins' => $joins,
			'group' => $group,
		));
		$allApplyLists = $this->find('all',array(
			'conditions' => array(
				'contest_id' => $searchCond['contest_id'],
			),
			'joins' => $joins,
			'group' => $group,
		));
		return array($data, $count, $allApplyLists);
	}


	public function saveApply($request ,$contest){


		$fields = array(
				'contest_id',
				'penname',
				'sex',
				'age',
				'job',
				'free',
				'work_type',
				'work_published',
				'title',
				'comment',
				'name_sei',
				'name_mei',
				'kana_sei',
				'kana_mei',
				'email',
				'tel1',
				'tel2',
				'tel3',
				'fax1',
				'fax2',
				'fax3',
				'zip1',
				'zip2',
				'pref',
				'address1',
				'address2',
				'ip',
				'ua',
				'udate',
				'material',
				'history',
				'member_id',
				'receipt_number',
				'pay_date',
				);
				// 応募歴が初めてだった場合入力フォームを空にする。
				if($request->data['history'] == 0){
					for ($i = 1; $i <= NUMBER_OF_TEXTAREA; $i++) {
						if($contest['fm_textarea'.$i.'_disp']){
							if($request->data['history'] == 0 && $contest['fm_textarea'.$i.'_name'] == "当作品の発表歴"){
								$request->data['textarea'.$i] = "";
							}
						}
					}
				}

				//選択項目の追加
				for ($i = 2; $i <= NUMBER_OF_FREE; $i++) {
					$fields[] = 'free'.$i;
				}

				//入力項目（1行）の追加
				for ($i = 1; $i <= NUMBER_OF_TEXT; $i++) {
					$fields[] = 'text'.$i;
				}

                //入力項目（複数行）の追加
				for ($i = 1; $i <= NUMBER_OF_TEXTAREA; $i++) {
					$fields[] = 'textarea'.$i;
				}

		$request->data["udate"] = date('Y-m-d H:i:s');

		// 受付番号保存
		$receipt_number_conditions = array(
			'contest_id' => $request->data['contest_id'],
			"NOT" => array(
				"Apply.receipt_number" => null
			));
		$receipt_number_fields = array('MAX(Apply.receipt_number)');
		$receipt_numbers = $this->find('first', array(
			'conditions' => $receipt_number_conditions,
			'fields' => $receipt_number_fields,
		));
		if (isset($receipt_numbers)) {
			foreach ($receipt_numbers[0] as $key => $value) {
				$receipt_number = $value;
			}
		}
		$request->data["receipt_number"] = $receipt_number+1;

		return $this->save($request->data, false, $fields);
	}

	public function saveApplyAdmin($request, $id){

		$request->data["id"] = $id;

		$fields = array(
				'penname',
				'sex',
				'age',
				'job',
				'free',
				'work_type',
				'work_published',
				'title',
				'comment',
				'name_sei',
				'name_mei',
				'kana_sei',
				'kana_mei',
				'email',
				'tel1',
				'tel2',
				'tel3',
				'fax1',
				'fax2',
				'fax3',
				'zip1',
				'zip2',
				'pref',
				'address1',
				'address2',
				'material',
				'history',
				'pay_date',
				'udate',
				'receipt_number',
				);

				//選択項目の追加
				for ($i = 2; $i <= NUMBER_OF_FREE; $i++) {
					$fields[] = 'free'.$i;
				}

				//入力項目（1行）の追加
				for ($i = 1; $i <= NUMBER_OF_TEXT; $i++) {
					$fields[] = 'text'.$i;
				}

                //入力項目（複数行）の追加
				for ($i = 1; $i <= NUMBER_OF_TEXTAREA; $i++) {
					$fields[] = 'textarea'.$i;
				}

		$request->data["udate"] = date('Y-m-d H:i:s');



		return $this->save($request->data, false, $fields);
	}

	public function delPersonal($contest_id){
		$this->updateAll(
				array(
						'name_sei' => NULL,
						'name_mei' => NULL,
						'kana_sei' => NULL,
						'kana_mei' => NULL,
						'email' => NULL,
						'tel1' => NULL,
						'tel2' => NULL,
						'tel3' => NULL,
						'fax1' => NULL,
						'fax2' => NULL,
						'fax3' => NULL,
						'zip1' => NULL,
						'zip2' => NULL,
						'pref' => NULL,
						'address1' => NULL,
						'address2' => NULL,
						'udate' => "'" . date('Y-m-d H:i:s') . "'"
				),
				array(
						'contest_id' => $contest_id,
				)
		);
	}
}
