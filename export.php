<?php
/*
    part-db version 0.1
    Copyright (C) 2005 Christoph Lechner
    http://www.cl-projects.de/

    part-db version 0.2+
    Copyright (C) 2009 K. Jacobs and others (see authors.php)
    http://code.google.com/p/part-db/

    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License
    as published by the Free Software Foundation; either version 2
    of the License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA

    $Id: export.php 511 2012-08-04 weinbauer73@gmail.com $
*/
    
    include ("lib.php");
    partdb_init();

    // database query

    $keyword    = smart_escape_for_search( $_REQUEST['keyword']);
    $search_nam = isset( $_REQUEST['search_nam']) ? $_REQUEST['search_nam'] == 'true' : false;
    $search_cat = isset( $_REQUEST['search_cat']) ? $_REQUEST['search_cat'] == 'true' : false;
    $search_des = isset( $_REQUEST['search_des']) ? $_REQUEST['search_des'] == 'true' : false;
    $search_com = isset( $_REQUEST['search_com']) ? $_REQUEST['search_com'] == 'true' : false;
    $search_sup = isset( $_REQUEST['search_sup']) ? $_REQUEST['search_sup'] == 'true' : false;
    $search_snr = isset( $_REQUEST['search_snr']) ? $_REQUEST['search_snr'] == 'true' : false;
    $search_loc = isset( $_REQUEST['search_loc']) ? $_REQUEST['search_loc'] == 'true' : false;
    $search_fpr = isset( $_REQUEST['search_fpr']) ? $_REQUEST['search_fpr'] == 'true' : false;

    $result = parts_select_search( $keyword, $search_nam, $search_cat, $search_des, $search_com, $search_sup, $search_snr, $search_loc, $search_fpr, true);

    $filename = "partdb_export_selection_". $_REQUEST["keyword"]; 


    if ( isset( $_REQUEST['format']))
    {
        $format = $_REQUEST['format'];
        $action = "output";
    }
    else
    {
        $action = "error";
        $error  = "Ausgabeformat nicht definiert";
    }


    if (( $action == "output") && ( $format == 'XML'))
    {

        // inspiration:
        // http://www.tsql.de/php/mysql_tabelle_export_xml_konvertieren

        $XMLDoc = new SimpleXMLElement( "<?xml version='1.0' encoding='UTF-8' standalone='yes'?><parts></parts>");

        //  catch SQL results, form XML output 
        while( $dbrow = mysql_fetch_object( $result))
        {
            $xmlrow = $XMLDoc->addChild( "part");
     
            foreach( $dbrow as $column => $value)
            {
                $xmlrow->$column = utf8_encode( $value);
            }
        }

        // convert to dom
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML( $XMLDoc->asXML());
     
        // output
        header("Content-Type: text/xml; charset=utf-8");
        header("Content-disposition: attachment; filename=\"". $filename .".xml\"");
        header("Pragma: no-cache");
        print $dom->saveXML();
    }
    

    if (( $action == "output") && ( $format == 'CSV'))
    {

        // header
        $CSVDoc = "# Kategorie; Name; Beschreibung; Anzahl; Footprint; Lagerort; Lieferant; Bestellnummer; Kommentar\n";
     
        //  catch SQL results, form CSV output 
        while( $dbrow = mysql_fetch_row( $result))
        {
            $CSVDoc .= implode( ";", $dbrow) . "\n";
        }
     

        // output
        header("Content-Type: text/x-csv");
        header("Content-disposition: attachment; filename=\"". $filename .".csv\"");
        header("Pragma: no-cache");

        print $CSVDoc;
    }


    if (( $action == "output") && ( $format == 'DokuWIKI'))
    {

        // header
        $CSVDoc = "^ Kategorie^ Name^ Beschreibung^ Anzahl^ Footprint^ Lagerort^ Lieferant^ Bestellnummer^ Kommentar^ \n|";
     
        //  catch SQL results, form DokuWIKI (CSV) output 
        while( $dbrow = mysql_fetch_row( $result))
        {
            $CSVDoc .= implode( " |", $dbrow) . "\n|";

        }
        // output
        header("Content-Type: text/plain");
        header("Content-disposition: attachment; filename=\"". $filename .".txt\"");
        header("Pragma: no-cache");

        print $CSVDoc;
    }


    if (( $action == "output") && ( $format == 'DymoCSV'))
    {

        // header
        $CSVDoc = "# Name; Footprint; Lagerort;\n";
     
        //  catch SQL results, form DokuWIKI (CSV) output 
        while( $dbrow = mysql_fetch_assoc( $result))
        {
            $CSVDoc .= $dbrow['name'] ."; ". $dbrow['footprint'] ."; ". $dbrow['location'] ."\n"; 
        }
     

        // output
        header("Content-Type: text/plain");
        header("Content-disposition: attachment; filename=\"". $filename .".txt\"");
        header("Pragma: no-cache");

        print $CSVDoc;
    }


    if ( $action == "error" )
    {
?>
        <html>
        <body>
        Fehler: <?php print $error ?>
        </body>
        </html>

<?php
    }
?>

