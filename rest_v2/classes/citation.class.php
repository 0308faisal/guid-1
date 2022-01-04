<?php
class Citation extends API
{

	public function __construct($request = null)
	{
		if(isset($request))
		{
			parent::__construct($request);
		}
	}

	/**
	 * @internal
	 */
	static function getCitations($id = null,$network=null)
	{
		$Db=$GLOBALS['Db'];
		$id = isset($id) ? $id : parent::args[0];
		$citations=array();
		$citationquery = "SELECT gr.guideline_id,n.name as network,gr.author,gr.citation as reference
											FROM guideline_references gr
											LEFT JOIN guideline_network_settings gns on gns.guideline_id=gr.guideline_id
											LEFT JOIN network n on n.id=gns.network_id
											WHERE gr.guideline_id='" . $id . "' and n.id='".$network."'";
		$citationresult = $Db->execute($citationquery);
		if($citationresult !== false)
		{
			if($Db->count() > 0)
			{
				foreach($citationresult as $row)
				{
					$citations[] = $row;
				}
			}
			return $citations;
		}
	}
}
