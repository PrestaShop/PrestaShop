<?php
class MenuTopLinks
{
  public static function gets($id_lang, $id_link = null)
  {
    return Db::getInstance()->ExecuteS('
    SELECT l.id_link, l.new_window, l.link, ll.label 
    FROM '._DB_PREFIX_.'linksmenutop l 
    LEFT JOIN '._DB_PREFIX_.'linksmenutop_lang ll ON (l.id_link = ll.id_link AND ll.id_lang = "'.$id_lang.'") 
    '.((!is_null($id_link)) ? 'WHERE l.id_link = "'.$id_link.'"' : '').'
    ');
  }

  public static function get($id_link, $id_lang)
  {
    return self::gets($id_lang, $id_link);
  }

  public static function add($link, $label, $newWindow = 0)
  {
    if(!is_array($label))
      return false;

    Db::getInstance()->autoExecute(
      _DB_PREFIX_.'linksmenutop',
      array(
        'new_window'=>(int)$newWindow,
        'link'=>$link
      ),
      'INSERT'
    );
    $id_link = Db::getInstance()->Insert_ID();
    foreach($label as $id_lang=>$label)
    {
      Db::getInstance()->autoExecute(
        _DB_PREFIX_.'linksmenutop_lang',
        array(
          'id_link'=>$id_link,
          'id_lang'=>$id_lang,
          'label'=>$label
        ),
        'INSERT'
      );
    }
  }

  public static function remove($id_link)
  {
    Db::getInstance()->delete(_DB_PREFIX_.'linksmenutop', "id_link = '{$id_link}'"); 
    Db::getInstance()->delete(_DB_PREFIX_.'linksmenutop_lang', "id_link = '{$id_link}'");
  }
}
?>