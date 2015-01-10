<?php
/**
 * Plugin Name: First Folio Citation
 * Plugin URI: 
 * Description: Shortcode to cite the Bodleian first folio data in a post
 * Version: 0.0.1
 * Author: Iain Emsley
 * Author URI: http://www.austgate.co.uk
 * License: GPL2
 */

/*  Copyright 2014  Iain Emsley  (email : iain_emsley@austgate.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

include 'ffparse.php';

//add quotation
add_shortcode( 'ffcite', 'ffquote_shortcode');

/**
*  Function to add the ffcite shortcode into Wordpress
*
*  @param Array $atts
*  Array of parameters
*/
function ffquote_shortcode($atts) {
  extract(
    shortcode_atts(
      array(
        'id' => '',
        'start' => '',
        'end' => '',
      ),
      $atts)
  );
 $folio = new ffparse();
 $fmt = new format();
 $quote = $folio->extract_quotation ($atts['id'], $atts['start'], $atts['end']);
 return $fmt->format_citation(sizeof($quote), $quote,$atts['id']);
}

class format {

/**
*  Array mapping the First Folio short codes into the full name
*/
private static $title= array(
     'tem' => 'The Tempest',
     'tgv' => 'The Two Gentlemen of Verona',
     'wiv' => 'The Merry Wives of Windsor',
     'mm'  => 'Measure for Measure',
     'err' => 'The Comedy of Errors',
     'ado' => 'Much Ado About Nothing',
     'lll' => 'Love\'s Labours Lost',
     'mnd' => 'A Midsummer Night\'s Dream',
     'mv'  => 'The Merchant of Venice',
     'ayl' => 'As You Like It',
     'shr' => 'The Taming of the Shrew',
     'tn'  => 'Twelfth Night',
     'wt'  => 'The Winter\'s Tale',
     'jn'  => 'King John',
     'r2'  => 'Richard II',
     '1h4' => 'Henry IV, Part 1',
     '2h4' => 'Henry IV, Part 2',
     'h5'  => 'Henry 5',
     '1h6' => 'Henry VI, Part 1',
     '2h6' => 'Henry VI, Part 2',
     'r3'  => 'Richard III',
     'h8'  => 'Henry VIII',
     'tro' => 'Troilus and Cressida',
     'cor' => 'Coriolanus',
     'tit' => 'Titus Andronicus',
     'rom' => 'Romeo and Juliet',
     'tim' => 'Timon of Athens',
     'jc'  => 'Julius Caesar',
     'mac' => 'Macbeth',
     'lr'  => 'King Lear',
     'oth' => 'Othello',
     'ant' => 'Antony and Cleopatra',
     'cym' => 'Cymbeline',
     'ham' => 'Hamlet',
   );

/**
*  Function to format the quotation
*
*  @param int $length
*  The length of the quotations array
*
*  @param Array $quotation
*  The quotation array
*
*  @param string $id
*  The shortcode id from the shortcode
*
*  @param Array $title
*  The array of the titles
*/
public function format_citation($length, $quotation,$id) {
  
  if ($length == '1') {

    return '"'. self::format_text($quotation[0]) .'" ' . '[' .self::format_title($id). '] '. $quotation[0]['title'] .' ('.$quotation[0]['act'].'.' . $quotation[0]['scene'].'.' . $quotation[0]['lineno'].')';

  } else if ($length == '2') {

    return '"'. self::format_text($quotation[0]) . '/'. self::format_text($quotation[1]) .'" ' . '[' .self::format_title($id). '] '. $quotation[0]['title'] 
           .' ('.$quotation[0]['act'].'.' . $quotation[0]['scene'] .'.' . $quotation[0]['lineno'] .'-'.$quotation[1]['lineno']. ')'; 

  } else {

    $folio = new ffparse();
    $t = '';
    foreach ($quotation as $line=>$text) {
      $t .= self::format_text($text) ."<br />";
      $act = $text['act'];
      $scene = $text['scene'];
    }
    $name = self::format_title($id);
    $title = $quotation[0]['title'];
    $line =  $quotation[0]['lineno'] .'&ndash;'. $quotation[max(array_keys($quotation))]['lineno'];
    return "<blockquote> $t
      <br /><footer>
      [$name] $title ($act . $scene . $line) <br />
      <a href='http://firstfolio.bodleian.ox.ac.uk'>" . $folio->cite . "</a>
      </footer>
      </blockquote>";
  }
}

/**
* Function to set the title tooltip
* @param array
* $textln is the array slice
*/
public function format_text($textln) {
  if ($textln['orig']) {
    $text = str_replace($textln['orig'], '', $textln['text']);
    return str_replace($textln['corr'], '<span title="'.$textln['orig'].'" style="text-decoration:underline">'.$textln['corr'].'</span>', $text);
  } 
  return $textln['text'];
}


/**
* Function to return the better known name
* @param string
* Shortcode string
* @return string
* Better known name of the play
*/
private function format_title($id) {
   return self::$title[$id];
}

}
