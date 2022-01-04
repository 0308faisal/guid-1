<?php

declare(strict_types=1);
/**
 * Guideline API
 *
 * @endpoint /guideline
 */
class Guideline extends API
{
	/**
	 * @internal
	 *
	 * @param null|mixed $request
	 */
	public function __construct($request = null)
	{
		if (isset($request)) {
			parent::__construct($request);
		}
	}

	/**
     * @OA\Get(
     *     path="/rest_v2/guideline/getguide/{id}",
     *     tags={"Guides"},
     *     summary="Enter your guide id",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The is your specific guides",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
	 *             
     *         )
     *     ),
	 * 
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid username/password supplied"
     *     ),
	 *  security={{"bearerAuth":{}}}
     * )
     */
	protected function getguide()
	{
		if ($this->method == 'GET') {
			if (isset($this->args[0]) && \is_numeric($this->args[0])) {
				$user = User::getuser();
				$guidelinequery = "
				SELECT g.id as guideline_id,g.parent_id,sn.name as parent_network,g.title,g.categories,g.short_desc,g.type,g.cdate as gcdate,gns.network_id, if(gns.allow_public=1,'true','false') as public, n.name as network_name, if(g.id=ga.guideline_id,'true','false') as adopted,gns.link,gns.network_publish
				FROM guideline g
				LEFT JOIN guideline_network_settings gns on (gns.guideline_id=g.id)
				LEFT JOIN guideline_network_settings sgns on (sgns.guideline_id=g.parent_id) or (sgns.guideline_id=g.id) 
				LEFT JOIN network sn on (sn.id=sgns.network_id)
				LEFT JOIN network_members nm on nm.network_id=gns.network_id
				LEFT JOIN members m on m.id=nm.member_id
				LEFT JOIN guideline_adoptions ga on (ga.guideline_id=g.id and ga.member_id=m.id)
				LEFT JOIN network n on n.id=gns.network_id
				LEFT JOIN network_files nf on nf.network_id=n.id
				LEFT JOIN guideline_comments gc on gc.guideline_id=g.id
				WHERE g.id='".$this->args[0]."' and (m.id='".$user['id']."' or gns.allow_public='1') limit 1";

				$guidelineresult = $this->Db->execute($guidelinequery);

				if ($guidelineresult !== false) {
					if ($this->Db->count() > 0) {
						$guideline = [];
						$guideline['id'] = $guidelineresult[0]['guideline_id'];
						$guideline['guideline_parent_id'] = $guidelineresult[0]['parent_id'];
						$guideline['guideline_parent_network'] = $guidelineresult[0]['parent_network'];
						$guideline['title'] = $guidelineresult[0]['title'];
						$guideline['categories'] = \explode(',', $guidelineresult[0]['categories']);
						$guideline['text'] = $guidelineresult[0]['short_desc'];
						$guideline['adopted'] = $guidelineresult[0]['adopted'];
						$guideline['link'] = $guidelineresult[0]['link'];
						$guideline['publish'] = $guidelineresult[0]['network_publish'];
						$guideline['date_created'] = $guidelineresult[0]['gcdate'];
						$guideline['network_id'] = $guidelineresult[0]['network_id'];
						$guideline['network'] = $guidelineresult[0]['network_name'];
						$guideline['network_logo'] = File::getFiles('network', $guidelineresult[0]['network_id']);
						$guideline['public'] = $guidelineresult[0]['public'];
						$guideline['citations'] = Citation::getCitations($guidelineresult[0]['guideline_id'], $guidelineresult[0]['network_id']);
						$guideline['contents'] = Content::getContent($guidelineresult[0]['guideline_id']);
						$guideline['files'] = File::getFiles('guideline', $guidelineresult[0]['guideline_id']);
						$guideline['comments'] = Comment::getComments($guidelineresult[0]['guideline_id']);
						$guideline['history'] = Log::getLog($guidelineresult[0]['guideline_id']);
						$guideline['type'] = $guidelineresult[0]['type'];
						$guideline['warningtitle'] = null;
						$guideline['warning'] = null;
						$key = $this->_findinarray($guideline['contents'], 'warning', '1');

						if ($key !== false) {
							$guideline['warningtitle'] = $guideline['contents'][$key]['title'];
							$guideline['warning'] = $guideline['contents'][$key]['content'];
							unset($guideline['contents'][$key]);
						}

						return \json_encode([
							$guideline,
							'status' => 'success',
							'response' => 'Guideline loaded successfully',
						]);
					}

					throw new Exception('No guidelines found');
				}

				throw new Exception($guidelinequery);
			}

			throw new Exception('Guideline ID required');
		}

		throw new Exception('Only accepts GET requests');
	}

	/**
	 * @internal
	 */
	protected function guidedelete()
	{
		if ($this->method == 'GET') {
			$user = User::getuser();
			$userid = $user['id'];
			$network_id = $this->request['nid'];

			if (isset($this->request['id']) && \is_numeric($this->request['id'])) {
				$guidelinequery = "SELECT id, published
												FROM guideline
												WHERE author='" . $userid . "' and id='" . $this->request['id'] . "'";
				$guidelineresult = $this->Db->execute($guidelinequery);

				if ($guidelineresult !== false) {
					if ($this->Db->count() > 0) {
						if ($guidelineresult[0]['published'] == 0) {
							$guidelinequery = "DELETE from guideline where id='" . $this->request['id'] . "'";
							$this->Db->execute($guidelinequery);
							$guidelinequery = "DELETE from guideline_adoptions where guideline_id='" . $this->request['id'] . "'";
							$this->Db->execute($guidelinequery);
							$guidelinequery = "DELETE from guideline_approval where guideline_id='" . $this->request['id'] . "'";
							$this->Db->execute($guidelinequery);
							$guidelinequery = "DELETE from guideline_category where guideline_id='" . $this->request['id'] . "'";
							$this->Db->execute($guidelinequery);
							$guidelinequery = "DELETE from guideline_comments where guideline_id='" . $this->request['id'] . "'";
							$this->Db->execute($guidelinequery);
							$guidelinequery = "DELETE from guideline_content where guideline_id='" . $this->request['id'] . "'";
							$this->Db->execute($guidelinequery);
							$guidelinequery = "DELETE from guideline_decision where guideline_id='" . $this->request['id'] . "'";
							$this->Db->execute($guidelinequery);
							$guidelinequery = "DELETE from guideline_files where guideline_id='" . $this->request['id'] . "'";
							$this->Db->execute($guidelinequery);
							$guidelinequery = "DELETE from guideline_network_settings where guideline_id='" . $this->request['id'] . "'";
							$this->Db->execute($guidelinequery);
							$guidelinequery = "DELETE from guideline_organizational_approval where guideline_id='" . $this->request['id'] . "'";
							$this->Db->execute($guidelinequery);
							$guidelinequery = "DELETE from guideline_references where guideline_id='" . $this->request['id'] . "'";
							$this->Db->execute($guidelinequery);

							return \json_encode([
								$this->request['id'],
								'status' => 'success',
								'response' => 'Guideline deleted successfully',
							]);
						}

						$guidelinequery = "update guideline set archive='1' where id='" . $this->request['id'] . "'";
						$this->Db->execute($guidelinequery);
						return \json_encode([
							$this->request['id'],
							'status' => 'success',
							'response' => 'Guideline archived successfully',
						]);
					}

					throw new Exception('Guideline not found');
				}

				throw new Exception('Database error when retrieving guideline');
			}

			throw new Exception('Invalid Guideline Id');
		}

		throw new Exception('Only accepts POST requests');
	}

	/**
	 * @internal
	 */
	protected function guideedit()
	{
		if ($this->method == 'POST') {
			$user = User::getuser();
			$userid = $user['id'];
			$network_id = $this->request['activenetwork'];

			if (isset($this->request['id']) && \is_numeric($this->request['id'])) {
				$guidelinequery = "SELECT g.id,g.published
												FROM guideline g
												LEFT JOIN guideline_network_settings gns on (gns.guideline_id=g.id)
												LEFT JOIN network_members nm on nm.network_id=gns.network_id
												WHERE g.id='" . $this->request['id'] . "' and nm.member_id='" . $userid . "' and nm.manager='1'";
				$guidelineresult = $this->Db->execute($guidelinequery);

				if ($guidelineresult !== false) {
					if ($this->Db->count() > 0) {
						$oldguide = $this->pullguide($this->request['id']);
						$guideline = [];
						$guideline_id = $guidelineresult[0]['id'];
						$guidetitle = $this->request['guidename'];
						$guideshortdesc = $this->request['guidedescription'];
						$guidecategories = $this->request['guidecategories'];

						if (isset($this->file['guidefile']['name']) && $this->file['guidefile']['name'] !== '') {
							$path = $this->file['guidefile']['name'];
							$ext = \pathinfo($path, \PATHINFO_EXTENSION);
							$filename = \sprintf('%s.%s', \md5($_FILES['guidefile']['tmp_name'] . \time()), $ext);
							$filesize = \filesize($_FILES['guidefile']['tmp_name']);

							if (FILESTORAGE == 'file') {
								if (!\move_uploaded_file($this->file['guidefile']['tmp_name'], DATA_DIR . '/uploads/' . $filename)) {
									throw new RuntimeException('Failed to move uploaded file.');
								}

								$guidefile = $filename;
								$guidefilepath = $path;
								$guidefileext = $ext;
								$guidefilecontent = '';
								$guidefilesize = $filesize;
							} elseif (FILESTORAGE == 'db') {
								$guidefile = $filename;
								$guidefilepath = $path;
								$guidefileext = $ext;
								$guidefilecontent = $this->Db->mysql_real_escape_equiv(\file_get_contents($_FILES['guidefile']['tmp_name']));
								$guidefilesize = $filesize;
							}
						}

						if (isset($this->request['filedelete']) && \is_array($this->request['filedelete'])) {
							$deletefiles = \implode("','", $this->request['filedelete']);
							$deletefiles = "'" . $deletefiles . "'";
						}
						$guidewarningtitle = $this->request['guidewarningtitle'];
						$guidewarning = $this->request['guidewarning'];

						if (isset($this->request['citationauthor'])) {
							foreach ($this->request['citationauthor'] as $key => $value) {
								if (!empty($value)) {
									if (isset($this->request['citationparentguidelineid'][$key])) {
										$guidecitation[$key]['parent_guideline_id'] = $this->request['citationparentguidelineid'][$key];
									} else {
										$guidecitation[$key]['parent_guideline_id'] = 0;
									}
									$guidecitation[$key]['author'] = $value;
									$guidecitation[$key]['reference'] = $this->request['citationreference'][$key];
								}
							}
						}

						if (isset($this->request['customcontenttitle'])) {
							foreach ($this->request['customcontenttitle'] as $key => $value) {
								$guidecustom[$key]['title'] = $value;
								$guidecustom[$key]['content'] = $this->request['customcontent'][$key];
							}
						}
						$guidepublish = isset($this->request['publish']) ? $this->request['publish'] : '0';
						$guideduplicate = isset($this->request['allowduplicate']) ? $this->request['allowduplicate'] : '0';
						$guideduplicatetype = isset($this->request['duplicatetype']) ? $this->request['duplicatetype'] : 'invite';
						$guidepreferredaction = 'adopt';
						$published = $guidelineresult[0]['published'] == 1 ? 1 : $guidepublish;
						$unique_id = \md5(\time() . \mt_rand(100, 99999));
						$current_date = \date('Y-m-d H:i:s');
						$guidelinequery = "UPDATE guideline
														SET unique_id='{$unique_id}',
														title='{$guidetitle}',
														short_desc='{$guideshortdesc}',
														categories='{$guidecategories}',
														preferred_action='{$guidepreferredaction}',
														mdate='{$current_date}',
														published='{$published}'
														WHERE id='{$guideline_id}'";
						$guidelineresult = $this->Db->execute($guidelinequery);

						if ($guidelineresult !== false) {
							if ($guideline_id > 0) {
								$guidelinenetworkquery = "UPDATE guideline_network_settings set allow_public='0',network_publish='{$guidepublish}',allow_duplicate='{$guideduplicate}',duplicate_type='{$guideduplicatetype}' where guideline_id='{$guideline_id}' and network_id='{$network_id}'";
								$guidelinenetworkresult = $this->Db->execute($guidelinenetworkquery);

								if (isset($guidefile)) {
									$guidelinefilequery = "INSERT INTO guideline_files (guideline_id,filename,dl_filename,filetype,content,filesize,cdate) VALUES ('{$guideline_id}','{$guidefile}','{$guidefilepath}','{$guidefileext}','{$guidefilecontent}','{$guidefilesize}','{$current_date}')";
									$guidelinefileresult = $this->Db->execute($guidelinefilequery);
								}

								if (isset($deletefiles)) {
									$guidelinefilequery = "DELETE FROM guideline_files where guideline_id='{$guideline_id}' and filename in ({$deletefiles})";
									$guidelinefileresult = $this->Db->execute($guidelinefilequery);

									if ($guidelinefileresult !== false) {
										foreach ($this->request['filedelete'] as $deletedfile) {
											if (FILESTORAGE == 'file') {
												\unlink(DATA_DIR . '/uploads/' . $deletedfile);
											}
										}
									}
								}
								$guidelinecitationquery = "DELETE from guideline_references where guideline_id='{$guideline_id}'";
								$guidelinecitationresult = $this->Db->execute($guidelinecitationquery);

								if (isset($guidecitation) && \is_array($guidecitation)) {
									foreach ($guidecitation as $key => $value) {
										$guidelinecitationquery = "INSERT INTO guideline_references (guideline_id,author,citation) VALUES ('{$guideline_id}','{$value['author']}','{$value['reference']}')";
										$guidelinecitationresult = $this->Db->execute($guidelinecitationquery);
									}
								}
								$guidelinecontentquery = "DELETE from guideline_content where guideline_id='{$guideline_id}'";
								$guidelinecontentresult = $this->Db->execute($guidelinecontentquery);

								if (isset($guidewarning) && $guidewarning !== '') {
									$guidelinewarningquery = "INSERT INTO guideline_content (guideline_id,title,content,warning) VALUES ('{$guideline_id}','{$guidewarningtitle}','{$guidewarning}','1')";
									$guidelinewarningresult = $this->Db->execute($guidelinewarningquery);
								}

								if (isset($guidecustom) && \is_array($guidecustom)) {
									foreach ($guidecustom as $key => $value) {
										$guidelinecustomquery = "INSERT INTO guideline_content (guideline_id,title,content) VALUES ('{$guideline_id}','{$value['title']}','{$value['content']}')";
										$guidelinecustomresult = $this->Db->execute($guidelinecustomquery);
									}
								}
								Log::logcomment($guideline_id, $userid, $this->request['logcomment']);
								$this->logguide($oldguide);
								return \json_encode([
									'status' => 'success',
									'response' => 'Guideline saved successfully',
									'guideline_id' => $guideline_id,
								]);
							}

							throw new Exception('Invalid Guideline Id');
						}

						throw new Exception('Error saving guideline');
					}

					throw new Exception('Guideline not found');
				}

				throw new Exception('Database error when retrieving guideline');
			}

			if (
				(isset($this->request['guidename']) && $this->request['guidename'] !== '') &&
				(isset($this->request['guidecategories']) && $this->request['guidecategories'] !== '')
			) {
				$guidetitle = $this->request['guidename'];
				$guideshortdesc = $this->request['guidedescription'];
				$guidecategories = $this->request['guidecategories'];
				$guidechanges = $this->request['logcomment'];

				if (isset($this->file['guidefile']['name']) && $this->file['guidefile']['name'] !== '') {
					$path = $this->file['guidefile']['name'];
					$ext = \pathinfo($path, \PATHINFO_EXTENSION);
					$filename = \sprintf('%s.%s', \md5($_FILES['guidefile']['tmp_name'] . \time()), $ext);
					$filesize = \filesize($_FILES['guidefile']['tmp_name']);

					if (FILESTORAGE == 'file') {
						if (!\move_uploaded_file($this->file['guidefile']['tmp_name'], DATA_DIR . '/uploads/' . $filename)) {
							throw new RuntimeException('Failed to move uploaded file.');
						}

						$guidefile = $filename;
						$guidefilepath = $path;
						$guidefileext = $ext;
						$guidefilecontent = '';
						$guidefilesize = $filesize;
					} elseif (FILESTORAGE == 'db') {
						$guidefile = $filename;
						$guidefilepath = $path;
						$guidefileext = $ext;
						$guidefilecontent = $this->Db->mysql_real_escape_equiv(\file_get_contents($_FILES['guidefile']['tmp_name']));
						$guidefilesize = $filesize;
					}
				}

				$guidewarningtitle = $this->request['guidewarningtitle'];
				$guidewarning = $this->request['guidewarning'];

				foreach ($this->request['citationauthor'] as $key => $value) {
					$guidecitation[$key]['author'] = $value;
					$guidecitation[$key]['reference'] = $this->request['citationreference'][$key];
				}

				foreach ($this->request['customcontenttitle'] as $key => $value) {
					$guidecustom[$key]['title'] = $value;
					$guidecustom[$key]['content'] = $this->request['customcontent'][$key];
				}
				$guidepublish = isset($this->request['publish']) ? $this->request['publish'] : '0';
				$guideduplicate = isset($this->request['allowduplicate']) ? $this->request['allowduplicate'] : '0';
				$guideduplicatetype = isset($this->request['duplicatetype']) ? $this->request['duplicatetype'] : 'invite';
				$guidepreferredaction = isset($this->request['preferredaction']) && $this->request['preferredaction'] !== '' ? $this->request['preferredaction'] : 'adopt';
				$published = $guidepublish;
				$unique_id = \md5(\time() . \mt_rand(100, 99999));
				$current_date = \date('Y-m-d H:i:s');
				$guidelinequery = "INSERT INTO guideline (unique_id,title,short_desc,categories,recent_changes,preferred_action,author,cdate,mdate,published) VALUES ('{$unique_id}','{$guidetitle}','{$guideshortdesc}','{$guidecategories}','{$guidechanges}','{$guidepreferredaction}','{$userid}','{$current_date}','{$current_date}','{$published}')";
				$guidelineresult = $this->Db->execute($guidelinequery);

				if ($guidelineresult !== false) {
					$guideline_id = $this->Db->lastInsertID();

					if ($guideline_id > 0) {
						$guidelinenetworkquery = "INSERT INTO guideline_network_settings (guideline_id,network_id,allow_public,network_publish,allow_duplicate,duplicate_type) VALUES ('{$guideline_id}','{$network_id}','0','{$guidepublish}','{$guideduplicate}','{$guideduplicatetype}')";
						$guidelinenetworkresult = $this->Db->execute($guidelinenetworkquery);

						if (isset($guidefile)) {
							$guidelinefilequery = "INSERT INTO guideline_files (guideline_id,filename,dl_filename,filetype,content,filesize,cdate) VALUES ('{$guideline_id}','{$guidefile}','{$guidefilepath}','{$guidefileext}','{$guidefilecontent}','{$guidefilesize}','{$current_date}')";
							$guidelinefileresult = $this->Db->execute($guidelinefilequery);
						}

						if (isset($guidecitation) && \is_array($guidecitation)) {
							foreach ($guidecitation as $key => $value) {
								$guidelinefilequery = "INSERT INTO guideline_references (guideline_id,author,citation) VALUES ('{$guideline_id}','{$value['author']}','{$value['reference']}')";
								$guidelinefileresult = $this->Db->execute($guidelinefilequery);
							}
						}

						if (isset($guidewarning) && $guidewarning !== '') {
							$guidelinewarningquery = "INSERT INTO guideline_content (guideline_id,title,content,warning) VALUES ('{$guideline_id}','{$guidewarningtitle}','{$guidewarning}','1')";
							$guidelinewarningresult = $this->Db->execute($guidelinewarningquery);
						}

						if (isset($guidecustom) && \is_array($guidecustom)) {
							foreach ($guidecustom as $key => $value) {
								$guidelinecustomquery = "INSERT INTO guideline_content (guideline_id,title,content) VALUES ('{$guideline_id}','{$value['title']}','{$value['content']}')";
								$guidelinecustomresult = $this->Db->execute($guidelinecustomquery);
							}
						}
						Log::logcomment($guideline_id, $userid, '<< Guideline Created >> - ' . $this->request['logcomment']);
						return \json_encode([
							'status' => 'success',
							'response' => 'Guideline added successfully',
							'guideline_id' => $guideline_id,
						]);
					}

					throw new Exception('Error creating guideline');
				}

				throw new Exception('Incomplete guideline details');
			}

			throw new Exception('Guideline name and category required');
		}

		throw new Exception('Only accepts POST requests');
	}

	/**
     * @OA\Get(
     *     path="/rest_v2/guideline/getguides",
     *     tags={"Get guides"},
     *     summary="Retrieve guideline listing",
     *     @OA\Response(
     *         response=200,
     *         description="Success - Guidesummary loaded successfully"
     *     ),
     *      security={{"bearerAuth":{}}}
     * )
     */
	protected function getguides()
	{
		if ($this->method == 'GET') {
			$user = User::getuser();

			$guidelinesearch = $categorysearch = $typequery = '';

			if (isset($this->request['searchterm']) && $this->request['searchterm'] !== '') {
				$guidelines = [];
				$guidelines = \array_merge($guidelines, $this->Db->searchdb('id', 'guideline', $this->request['searchterm']));
				$guidelines = \array_merge($guidelines, $this->Db->searchdb('guideline_id', 'guideline_content', $this->request['searchterm']));

				if (!empty($guidelines)) {
					$guidelines = \implode(',', $guidelines);
					$guidelinesearch = ' and g.id in (' . $guidelines . ')';
				} else {
					throw new Exception('No guidelines found');
				}
			}

			if (isset($this->request['category']) && $this->request['category'] !== '') {
				$categorysearch = " and g.categories like '%" . $this->request['category'] . "%'";
			}

			if ($user['admin'] !== 1) {
				$typequery .= " and (n.network_type = 'public' or n.network_type = 'private')";
			}
			$guidelinequery = "SELECT g.id,g.categories as category,g.title,g.author,n.id as network_id,n.name as network,gns.allow_public,gns.network_publish,gns.link
				FROM members m
				LEFT JOIN network_members nm on nm.member_id=m.id
				LEFT JOIN network n on n.id=nm.network_id
				LEFT JOIN guideline_network_settings gns on (gns.network_id=nm.network_id or gns.allow_public=1)
				LEFT JOIN guideline_adoptions ga on (ga.member_id=m.id and gns.guideline_id=ga.guideline_id)
				LEFT JOIN guideline g on g.id=coalesce(gns.guideline_id,ga.guideline_id)
				WHERE m.id='" . $user['id'] . "'" . $guidelinesearch . $typequery . $categorysearch . " and g.archive=0 and (nm.status='active' or gns.allow_public='1')
				ORDER BY category, title ASC";

			$guidelineresult = $this->Db->execute($guidelinequery);

			if ($guidelineresult !== false) {
				if ($this->Db->count() > 0) {
					$guidelinecount = $this->Db->count();
					$adoptedguidelines = $orphanedguidelines = $unpublishedguidelines = [];

					foreach ($guidelineresult as $guideline) {
						if ($guideline['allow_public'] == '0' && $guideline['network_publish'] == '0' && $guideline['author'] == $user['id']) {
							$unpublishedguidelines[] = $guideline;
						} elseif ($guideline['allow_public'] == '0' && $guideline['network_publish'] == '0' && $guideline['author'] !== $user['id']) {
							--$guidelinecount;
						} else {
							$publishedguidelines[] = $guideline;
						}
					}

					return \json_encode([
						'available_guide_count' => $guidelinecount,
						'published_guide_count' => \count($publishedguidelines),
						'published_guides' => $publishedguidelines,
						'unpublished_guides' => $unpublishedguidelines,
						'status' => 'success',
						'response' => 'Guidesummary loaded successfully',
					]);
				}

				throw new Exception('No guidelines found');
			}

			throw new Exception('Database error when retrieving networks' . $this->Db->getError());
		}

		throw new Exception('Only accepts GET requests');
	}

	/**
	 * @internal
	 */
	protected function guidemanagementsummary()
	{
		if ($this->method == 'GET') {
			$user = User::getuser();
			$guidelinesearch = $categorysearch = $typequery = '';

			if (isset($this->request['searchterm']) && $this->request['searchterm'] !== '') {
				$guidelines = [];
				$guidelines = \array_merge($guidelines, $this->Db->searchdb('id', 'guideline', $this->request['searchterm']));
				$guidelines = \array_merge($guidelines, $this->Db->searchdb('guideline_id', 'guideline_content', $this->request['searchterm']));

				if (!empty($guidelines)) {
					$guidelines = \implode(',', $guidelines);
					$guidelinesearch = ' and g.id in (' . $guidelines . ')';
				} else {
					throw new Exception('No guidelines found');
				}
			}

			if ($user['admin'] !== 1) {
				$typequery .= " and (n.network_type = 'public' or n.network_type = 'private')";
			}
			$guidelinequery = "SELECT distinct g.id,g.parent_id,g.categories as category, g.title,g.author,n.id as network_id, n.name as network, gns.allow_public,gns.network_publish,gns.link FROM guideline g
									JOIN guideline_network_settings gns on gns.guideline_id=g.id
									JOIN community_access ca on ca.network_id=gns.network_id and ca.status='active'
									JOIN network n on n.id=gns.network_id
									WHERE g.archive=0" . $guidelinesearch . $typequery;
			' ORDER BY category,title ASC';

			$guidelineresult = $this->Db->execute($guidelinequery);

			if ($guidelineresult !== false) {
				if ($this->Db->count() > 0) {
					$guidelinecount = $this->Db->count();
					$publishedguidelines = $unpublishedguidelines = [];
					foreach ($user['networks'] as $key => $network) {
						$usernetworks[] = $network['id'];
						$manager[$network['id']] = $network['manager'];
					}
					foreach ($guidelineresult as $guideline) {
						if ($guideline['allow_public'] == '0' && $guideline['network_publish'] == '0' && (isset($manager[$guideline['network_id']]) && $manager[$guideline['network_id']] == true)) {
							$unpublishedguidelines[] = $guideline;
						} elseif ($guideline['allow_public'] == '0' && $guideline['network_publish'] == '0' && (!isset($manager[$guideline['network_id']]) || $manager[$guideline['network_id']] != true)) {
							--$guidelinecount;
						} else {
							$publishedguidelines[] = $guideline;
						}
					}

					return \json_encode([
						'available_guide_count' => $guidelinecount,
						'published_guide_count' => \count($publishedguidelines),
						'published_guides' => $publishedguidelines,
						'unpublished_guides' => $unpublishedguidelines,
						'status' => 'success',
						'response' => 'Guidesummary loaded successfully',
					]);
				}

				throw new Exception('No guidelines found');
			}

			throw new Exception('Database error when retrieving guidelines - ' . $guidelinequery);
		}

		throw new Exception('Only accepts GET requests');
	}

	protected function logguide($guide)
	{
		$citations = $guide['citations'];
		$contents = $guide['contents'];
		$files = $guide['files'];
		unset($guide['citations'], $guide['contents'], $guide['files']);
		$columns = \implode(', ', \array_keys($guide));
		$values = \implode("', '", \array_values($guide));
		$guidelinequery = "INSERT into log_guideline (${columns}) values('${values}')";
		$guidelineresult = $this->Db->execute($guidelinequery);

		if ($guidelineresult !== false) {
			if (isset($citations)) {
				foreach ($citations as $key => $value) {
					$citationquery = "INSERT into log_guideline_references (guideline_id,author,citation,mdate) values('{$guide['id']}','{$value['author']}','{$value['reference']}','" . \date('Y-m-d H:i:s') . "')";
					$citationresult = $this->Db->execute($citationquery);
				}
			}

			if (isset($contents)) {
				foreach ($contents as $key => $value) {
					$contentquery = "INSERT into log_guideline_content (guideline_id,title,content,warning,mdate) values('{$guide['id']}','{$value['title']}','{$value['content']}','{$value['warning']}','" . \date('Y-m-d H:i:s') . "')";
					$contentresult = $this->Db->execute($contentquery);
				}
			}

			if (isset($files)) {
				foreach ($files as $key => $value) {
					$filequery = "INSERT into log_guideline_files (guideline_id,filename,dl_filename,cdate,filetype,filesize,mdate) values('{$guide['id']}','{$value['filename']}','{$value['dl_filename']}','{$value['cdate']}','{$value['filetype']}','{$value['filesize']}','" . \date('Y-m-d H:i:s') . "')";
					$fileresult = $this->Db->execute($filequery);
				}
			}
			return true;
		}

		return $this->Db->getError();
	}

	/**
	 * @internal
	 */
	protected function pullguide($id)
	{
		$guidelinequery = "SELECT g.*
								FROM guideline g
								WHERE g.id='" . $id . "'
								";

		$guidelineresult = $this->Db->execute($guidelinequery);

		if ($guidelineresult !== false) {
			if ($this->Db->count() > 0) {
				$guideline = [];
				$guidelineresult[0]['mdate'] = \date('Y-m-d H:i:s');
				$guideline = $guidelineresult[0];
				$guideline['citations'] = Citation::getCitations($guideline['id'], $guideline['network_id']);
				$guideline['contents'] = Content::getContent($guideline['id']);
				$guideline['files'] = File::getFiles('guideline', $guideline['id']);

				return $guideline;
			}

			return false;
		}

		return false;
	}

	/**
	 * @internal
	 */
	protected function pullnetworkguides($network_id)
	{
		$guidelinequery = "SELECT g.*
								FROM guideline g
								JOIN guideline_network_settings gns on gns.guideline_id=g.id
								WHERE gns.network_id like '%" . $network_id . ",%'
								";

		$guidelineresult = $this->Db->execute($guidelinequery);

		if ($guidelineresult !== false) {
			if ($this->Db->count() > 0) {
				foreach ($guidelineresult as $key => $guideline) {
					$guideline['citations'] = $this->getCitations($guideline['id'], $network_id);
					$guideline['contents'] = $this->getContent($guideline['id']);
					$guideline['files'] = $this->getFiles('guideline', $guideline['id']);
					$guidelineresult[$key] = $guideline;
				}
				return $guidelineresult;
			}

			return false;
		}

		return false;
	}

	/**
	 * @internal
	 */
	protected function cloneall()
	{
		if ($this->method == 'GET') {
			$user = User::getuser();
			$network_id = $user['networks'][0]['network_id'];
			$guides = $this->pullnetworkguides($this->request['network_id']);

			foreach ($guides as $key => $guide) {
				$guide['parent_id'] = $guide['id'];
				$guide['type'] = 'copy';
				$guide['author'] = $user['id'];
				$citations = $guide['citations'];
				$contents = $guide['contents'];
				$files = $guide['files'];
				unset($guide['id'], $guide['citations'], $guide['contents'], $guide['files']);
				$columns = \implode(', ', \array_keys($guide));
				$values = \implode("', '", \array_values($this->_sanitize($guide)));
				$guidelinequery = "INSERT into guideline (${columns}) values('${values}')";
				$guidelineresult = $this->Db->execute($guidelinequery);

				if ($guidelineresult !== false) {
					$guide['id'] = $this->Db->lastInsertID();
					$guidelinenetworkquery = "INSERT INTO guideline_network_settings (guideline_id,network_id,allow_public,network_publish,allow_duplicate,duplicate_type) VALUES ('{$guide['id']}','{$network_id}','0','0','0','invite')";
					$guidelinenetworkresult = $this->Db->execute($guidelinenetworkquery);

					foreach ($citations as $key => $value) {
						$citationquery = "INSERT into guideline_references (guideline_id,author,citation) values('{$guide['id']}','{$value['author']}','{$value['reference']}')";
						$citationresult = $this->Db->execute($citationquery);
					}

					foreach ($contents as $key => $value) {
						$value['orderno'] = $value['orderno'] !== '' ? $value['orderno'] : 0;
						$contentquery = "INSERT into guideline_content (guideline_id,title,content,warning,orderno) values('{$guide['id']}','{$value['title']}','{$value['content']}','{$value['warning']}','{$value['orderno']}')";
						$contentresult = $this->Db->execute($contentquery);
					}

					foreach ($files as $key => $value) {
						$filequery = "INSERT into guideline_files (guideline_id,filename,dl_filename,cdate,filetype,filesize) values('{$guide['id']}','{$value['filename']}','{$value['dl_filename']}','{$value['cdate']}','{$value['filetype']}','{$value['filesize']}')";
						$fileresult = $this->Db->execute($filequery);
					}
					Log::logcomment($guide['id'], $user['id'], '<< Guideline copied/linked to your network >>');
				} else {
					throw new Exception('Problem cloning guideline');
				}
			}
			return \json_encode([
				'status' => 'success',
				'response' => 'Guides Successfully Cloned',
			]);
		}
	}

	/**
	 * @internal
	 */
	protected function cloneguide()
	{
		if ($this->method == 'GET') {
			$user = User::getuser();
			$network_id = $this->request['activenetwork'];
			$guide = $this->pullguide($this->request['id']);
			$guide['parent_id'] = $this->request['id'];
			$guide['type'] = 'copy';
			$guide['author'] = $user['id'];
			$citations = $guide['citations'];
			$contents = $guide['contents'];
			$files = $guide['files'];
			unset($guide['id'], $guide['citations'], $guide['contents'], $guide['files']);
			$columns = \implode(', ', \array_keys($guide));
			$values = \implode("', '", \array_values($guide));
			$guidelinequery = "INSERT into guideline (${columns}) values('${values}')";
			$guidelineresult = $this->Db->execute($guidelinequery);

			if ($guidelineresult !== false) {
				$guide['id'] = $this->Db->lastInsertID();
				$guidelinenetworkquery = "INSERT INTO guideline_network_settings (guideline_id,network_id,allow_public,network_publish,allow_duplicate,duplicate_type) VALUES ('{$guide['id']}','{$network_id}','0','0','0','invite')";
				$guidelinenetworkresult = $this->Db->execute($guidelinenetworkquery);

				if (isset($citations)) {
					foreach ($citations as $key => $value) {
						$citationquery = "INSERT into guideline_references (guideline_id,parent_guideline_id,author,citation) values('{$guide['id']}','{$guide['parent_id']}','{$value['author']}','{$value['reference']}')";
						$citationresult = $this->Db->execute($citationquery);
					}
				}

				if (isset($contents)) {
					foreach ($contents as $key => $value) {
						$value['orderno'] = isset($value['orderno']) && $value['orderno'] !== '' ? $value['orderno'] : 0;
						$contentquery = "INSERT into guideline_content (guideline_id,title,content,warning,orderno) values('{$guide['id']}','{$value['title']}','{$value['content']}','{$value['warning']}','{$value['orderno']}')";
						$contentresult = $this->Db->execute($contentquery);
					}
				}

				if (isset($files)) {
					foreach ($files as $key => $value) {
						$filequery = "INSERT into guideline_files (guideline_id,filename,dl_filename,cdate,filetype,filesize) values('{$guide['id']}','{$value['filename']}','{$value['dl_filename']}','{$value['cdate']}','{$value['filetype']}','{$value['filesize']}')";
						$fileresult = $this->Db->execute($filequery);
					}
				}
				Log::logcomment($guide['id'], $user['id'], '<< Guideline copied/linked to your network >>');
				return \json_encode([
					'status' => 'success',
					'response' => 'Guide Successfully Cloned',
					'id' => $guide['id'],
				]);
			}

			throw new Exception('Problem cloning guideline');
		}
	}

	/**
	 * @internal
	 */
	protected function linkguide()
	{
		if ($this->method == 'GET') {
			$guideid = $this->request['id'];
			$user = User::getuser();
			$network_id = $this->request['activenetwork'];
			$guidelinenetworkquery = "INSERT INTO guideline_network_settings (guideline_id,network_id,allow_public,network_publish,allow_duplicate,duplicate_type,link) VALUES ('{$guideid}','{$network_id}','0','1','0','invite','1')";
			$guidelinenetworkresult = $this->Db->execute($guidelinenetworkquery);
			Log::logcomment($guideid, $user['id'], '<< Guideline copied/linked to your network >>');
			return \json_encode([
				'status' => 'success',
				'response' => 'Guide Successfully Linked',
				'id' => $guideid,
			]);
		}
	}

	/**
	 * @internal
	 */
	protected function unlinkguide()
	{
		if ($this->method == 'GET') {
			$guideid = $this->request['id'];
			$user = User::getuser();
			$network_id = $this->request['activenetwork'];
			$guidelinenetworkquery = "DELETE FROM guideline_network_settings where guideline_id='{$guideid}' and network_id='{$network_id}' and link='1'";
			$guidelinenetworkresult = $this->Db->execute($guidelinenetworkquery);
			Log::logcomment($guideid, $user['id'], '<< Guideline unlinked from your network >>');
			return \json_encode([
				'status' => 'success',
				'response' => 'Guide Successfully Unlinked',
				'id' => $guideid,
			]);
		}
	}

	/**
     * @OA\Get(
     *     path="/rest_v2/guideline/adopt/{id}",
     *     tags={"Adopt"},
     *     summary="Bookmark specified guideline",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Guideline id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
	 * 
     *     @OA\Response(
     *         response=200,
     *         description="Success - Bookmark Successful"
     *     ),
	 *  security={{"bearerAuth":{}}}
     * )
     */
	protected function adopt()
	{
		if ($this->method == 'GET') {
			if (isset($this->args[0]) && \is_numeric($this->args[0])) {
				$user = User::getuser();
				$adoptquery = "SELECT guideline_id,member_id FROM guideline_adoptions where guideline_id='" . $this->args[0] . "' and member_id='" . $user['id'] . "'";
				$adoptresult = $this->Db->execute($adoptquery);

				if ($adoptresult !== false) {
					if ($this->Db->count() == 0) {
						$adoptquery = "INSERT into guideline_adoptions (guideline_id,member_id) values('{$this->args[0]}','{$user['id']}')";
						$adoptresult = $this->Db->execute($adoptquery);

						if ($adoptresult == false) {
							throw new Exception('Database error when setting adoption');
						}

						return \json_encode([
							'status' => 'success',
							'response' => 'Bookmark Successful',
						]);
					}

					throw new Exception('Guideline was already adopted');
				}

				throw new Exception('Database error when retrieving adoptions');
			}
		} else {
			throw new Exception('Only accepts GET requests');
		}
	}

	/**
     * @OA\Get(
     *     path="/rest_v2/guideline/unadopt/{id}",
     *     tags={"UnAdopt"},
     *     summary="Remove specified guideline from bookmarks",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Guideline id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
	 * 
     *     @OA\Response(
     *         response=200,
     *         description="Success - Bookmark Removed"
     *     ),
	 *  security={{"bearerAuth":{}}}
     * )
     */
	protected function unadopt()
	{
		if ($this->method == 'GET') {
			if (isset($this->args[0]) && \is_numeric($this->args[0])) {
				$user = User::getuser();
				$userquery = "DELETE ga from guideline_adoptions ga join members m on m.id=ga.member_id where m.id='" . $user['id'] . "' and ga.guideline_id='" . $this->args[0] . "'";
				$userresult = $this->Db->execute($userquery);

				if ($userresult !== false) {
					if ($this->Db->affectedRows() == 1) {
						return \json_encode([
							'status' => 'success',
							'response' => 'Bookmark Removed',
						]);
					}

					throw new Exception('Guideline not found');
				}

				throw new Exception('Database error when retrieving user');
			}

			throw new Exception('Guideline ID required');
		}

		throw new Exception('Only accepts GET requests');
	}
}
