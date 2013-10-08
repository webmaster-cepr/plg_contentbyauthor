<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
class plgContentContentByAuthor extends JPlugin {


        public function onContentPrepare($context, &$row, &$params ) {
		
		$db = JFactory::getDBO();
        
		if (preg_match_all("#{author}(.*?){/author}#s", $row->text, $matches, PREG_PATTERN_ORDER) > 0) {
		
			foreach( $matches[0] as $match ){
			
				$_temp = preg_replace("/{.+?}/", "", $match);
				unset($_params);
				$_params = explode(":",$_temp);
				$_author = $_params[0];
				$_year = $_params[1];
				$_cat = html_entity_decode($_params[2]);
				$_limit = $_params[3];
				$i = 0;
				$now = date('Y-m-d H:i:s');
				
 				$sql = "SELECT id, title, alias, introtext AS intro, catid, DATE_FORMAT(created, '%M %d, %Y') AS created, created AS sortdate "
				. "\n FROM #__content "
				. "\n WHERE state = 1 ";
				
				if( $_author ) {
					$sql .= "\n AND created_by_alias LIKE '%" . $_author . "%' ";
				}
				if( $_year ){
					$sql .= "\n AND created BETWEEN '" . $_year . "-01-01' AND '" . $_year . "-12-31' ";
				}
				if( $_cat ){
					$sql .= "\n AND catid = " . $_cat . " ";
				}				

				$sql .= "\n ORDER BY sortdate DESC";

				$db->setQuery($sql);				
				$items = $db->loadObjectList();

				$html = '';

				foreach($items as $item ){

					$url = JRoute::_('index.php?view=article&id=' . $item->id . '&catid=' . $item->catid);

                    $html .= '<p><a href="'.$url . '">'.$item->title.'</a><br />'
                    ."\n".$item->intro.'</p>';
                    $i++;

					if ($i == $_limit) { break; }
					
				}
				
				$row->text = preg_replace( "#{author}".$_temp."{/author}#s", $html , $row->text );				
			
			
			}
		
		}
	}
}	
