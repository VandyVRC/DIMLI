DIMLI
=====

Digital Media Management Library

Project Description
-------------------

A web-based application for the cataloging of cultural objects, developed specifically for use by Visual Resources professionals, which manages workflow and facilitates a singularly rich cataloging experience and powerful data exports.

DIMLI is currently used by the Visual Resources Center and the Department of History of Art at Vanderbilt University to process and deliver new digitization orders, and also to perform a recataloging of thousands of old image records in order to conform with the Visual Resources Association's Core 4 standards for the cataloging of cultural objects.

DIMLI has facilitated our massive recataloging endeavor by allowing us to establish Work and Image record relationships where before we had none, and by introducing an order management system that enables us to more easily assign and track workflow tasks.

To-Do List
----------

#### "Must-Have" Features:

+ Data imports

#### "Would Be Nice" Features:

+ Printer-friendly Order paper-trail

+ Activity statistics for Administrators

Installation Instructions
-------------------------

In order to install DIMLI, you will need a server running MySQL on which to create a database, and a server running PHP on which to host the application itself. These two functions can, of course, be fulfilled by the same server, though local practices might dictate a seperation of these aspects of the system.

#### Import Database Schema

DIMLI's database schema is provided by the dump file `_sql/dimli_schema.sql`

Use a command line prompt (or graphical user interface for the administration of SQL databases, such as phpMyAdmin) to create a new SQL database on your server. I suggest calling the database "dimli", but you may call the database whatever you wish. Then import/run the schema file to create the many tables that comprise DIMLI's relational structure.

All data created by, and imported into, DIMLI will reside within these tables and the within the SQL database you just created.

#### Define Unique Constants

1) Create a new file called `_php/_config/constants.inc.php`

2) Insert the following contents into the file:

    <?php
    /* 
    DATABASE CONSTANTS
    ------------------
    Define the specifics of your server environment */

    define('DB_SERVER', '{your.server.url.here}');
    define('DB_USER', '{yourDatabaseUsername}');
    define('DB_PASS', '{yourDatabasePassword}');
    define('DB_NAME', '{yourDatabaseName}');

    $DB_NAME = DB_NAME;

    
    ROOT DOMAIN OF YOUR DIMLI DIRECTORY 
    ----------------------------------------------
        Examples:
        "http://dimli.library.vanderbilt.edu"
        "http://mywebsite.com"  */

    define('webroot','{yourDomain}');
    $webroot = webroot;
    
    /*
    DEFINE SITE TITLE
    ----------------------------------------------
        Example:

        "dimli: Library Exhibits" */

    define('site_title', 'dimli');
    $site_title = site_title;

    /*
    DEFINE ENCRYPTION SALT
    ----------------------
    Define a salt parameter to pass into the crypt function */

    define('SALT', '19ReXiNSuLaRuM23');

    /*
    DEFINE IMAGE FILEPATH
    ---------------------
    Define the filepath for the directory that stores your JPG archive 
    Examples:
   "../MyImageFiles/"
   "..images/"
   "..exhibits/images/"     
   "http://hosted.image.repository.edu/images/" */

    define('IMAGE_DIR', '{filepathOfYourImageDirectory}');
    $image_dir = IMAGE_DIR;

    /* 
    DEFINE IMAGE FOLDER OR EXTERNAL SOURCE FOR TIMTHUMB PLUGIN
    ---------------------------------------------------------
        Examples:
       "MyImageFiles/"
       "images/"
       "exhibits/images/"  
       "http://hosted.image.repository.edu/images/" - if your image source is external - SEE NOTE BELOW */

    define('IMAGE_SRC', '{imagesFolderOrPath}');
    $image_src = IMAGE_SRC;

    /* 
    OTHER CONSTANTS
    ---------------
    */
    
    define('DIR', dirname(__DIR__).'/');

3) Replace each of the sections in the above code that are wrapped in `{` and `}` (replacing brackets but leaving single quotation marks) with the appropriate values for your server environment.

4) Save and allow `_php/_config/constants.inc.php` to remain at this directory location. It will be required by several files in the application at large.

#### Prepare Image Directory

Wherever you choose to store your images, either within the application or externally (as defined in _php/_config/constants.php), Dimli currently requires jpg images. DIMLI will look in this directory location for image files used throughout the application.


#### Note On External Image Sources and the timthumb Plugin

If your image directory exists in another location, via url, you must edit the timthumb.php file located in the timthumb folder in the plugins directory. Find the following line of code: 

"if(! defined('ALLOW_EXTERNAL') )       define ('ALLOW_EXTERNAL', FALSE);"

Change it to this: 

"if(! defined('ALLOW_EXTERNAL') )       define ('ALLOW_EXTERNAL', TRUE);"

Then add the external site name to the list of "ALLOWED SITES," found in the same file, which looks like this:

if(! isset($ALLOWED_SITES)){
   $ALLOWED_SITES = array (
      'flickr.com',
      'staticflickr.com',
      'picasa.com',
      'img.youtube.com',
      'upload.wikimedia.org',
      'photobucket.com',
      'imgur.com',
      'imageshack.us',
      'tinypic.com',
      'addyoursitehere.com'
   );
}

#### Making PowerPoints Available to End-Users

As you complete/deliver orders, manually add a PowerPoint file for each order to the _ppts folder. Each PowerPoint file should be named with a four-digit number matching the order number (e.g., 2363.pptx). This file will then be available for end-users to download when they visit their homepage.

#### Log In as the Default Admin

username/password: `admin`/`admin`

#### Accessing and Utilizing the Getty Vocabularies

As a result of git file size constraints, we are unable to include the SQL files for the data to fill the Getty Vocabularies tables. We have made these SQL files available via a public Dropbox. 

https://dl.dropboxusercontent.com/u/104573827/Getty%20Vocabularies.zip

We have also included a copy of our repositories data, in case anyone might like to utilize them. 

These Getty Vocabularies are too large to import via phpMyAdmin. However, they can be imported via the MySQL command line.   


Export CSV Fields Explained
---------------------------

When using DIMLI's Export feature, a user will generate a CSV file that consists of twenty-seven individual fields which descibe each image record. These fields are explained below.

The CSV's schema was developed in order to remain consistent with an existing installation of Madison Digital Image Database (MDID) at Vanderbilt University and adopts the field names which correspond with those which were used for past imports into that system (a new installation of DIMLI could rename these fields by modifying _php/_export/export_records.php lines 1183-1209 to reflect the field names you would prefer to use). The names of the fields are relatively unimportant provided that the Digital Asset Management (DAM) system into which you are importing the cataloging data allows you to manually map each of the fields in DIMLI's export CSV to the appropriate location in the DAM.

    Identifier

The six-digit record id number associated with this particular image record.

    resource

Same as identifier, but with ".jpg" appended to the six-digit record id. This field can be used to instruct the Digital Asset Manager where to find image files.

    vra.title

A semi-colon-separated list of titles. This is a comprehenisve list of the titles of the work record which is related to the image at hand.

    vra.imagetitle

A semi-colon-separated list of titles. This is a comprehenisve list of the titles of the image record itself.

    vra.agent

A semi-colon-separated list of agent names. Each agent name is formatted 'Lastname, Firstname' and followed by a parenthetical comma-separated list of roles which this particular agent filled in the making of this image, or its related work record. No distinction is made between image-record agents and work-record agents - they are simply strung together.

    agentALT

A semi-colon-separated list of agent names. These are drawn from the Getty Union List of Artist Names and represent every documented alternative spelling of the agent names found in the 'vra.agent' field above.

    vra.date

A semi-colon-separated list of dates. Rough dates begin with 'ca.' and the start and end dates of time spans are separated by a hyphen. Each date is followed by a parethetical type description, denoting the event which the date describes.

    vra.technique

A semi-colon-separated list of techniques. These are drawn from the cataloging entry for the image record at hand, and its related work record. Some technique terms are followed by a parenthetical qualifier that distinguishes this term from other simliar terms in the Getty Art and Architecture Thesaurus.

   techniqueALT

 A semi-colon-separated list of techniques. These are drawn from the Getty Art and Architecture Thesaurus and represent every documented alternative spelling of the technique terms found in the 'vra.technique' field above.

    vra.worktype

A semi-colon-separated list of work types. These are drawn from the cataloging entry for the image record at hand, and its related work record. Some work type terms are followed by a parenthetical qualifier that distinguishes this term from other simliar terms in the Getty Art and Architecture Thesaurus.

    worktypeALT

A semi-colon-separated list of work types. These are drawn from the Getty Art and Architecture Thesaurus and represent every documented alternative spelling of the work type terms found in the 'vra.worktype' field above.

    vra.material

A semi-colon-separated list of materials. These are drawn from the cataloging entry for the image record at hand, and its related work record. Some material terms are followed by a parenthetical qualifier that distinguishes this term from other simliar terms in the Getty Art and Architecture Thesaurus.

    materialALT

A semi-colon-separated list of materials. These are drawn from the Getty Art and Architecture Thesaurus and represent every documented alternative spelling of the material terms found in the 'vra.material' field above.

    vra.measurementsWORK

A semi-colon-separeted list of measurements which describe the image's parent work record. Each measurement is followed by a parenthetical type which clarifies in what manner the measurement describes the object.

   vra.measurementIMAGE

 A semi-colon-separeted list of measurements which describe the image record itself. Each measurement is followed by a parenthetical type which clarifies in what manner the measurement describes the object. These entries are often populated with the default 'None' entry because most image records gather most of their descriptive information from their parent work record, except when the object described by the image record is dramatically different from the object described by the work record.

    vra.location

A semi-colon-separated list of locations. These are drawn from the cataloging entry for the image record at hand, and its related work record. Each location is followed by a parenthetical qualifier that distinguishes this location from other simliarly named locations in the Getty Thesaurus of Geographic Names, as well as a second parenthetical type which clarifies the relationship that the location has with the object at hand.

more coming ...

More Information
----------------

The [YouTube channel] [1] for this project contains a short [introductory video] [2] as well as several feature highlights that delve deeper into individual aspects of the application.

[1]: http://www.youtube.com/channel/UCNavkQ4OuUO2idBjNfaq2zg
[2]: http://www.youtube.com/watch?v=k34agI23-jg
