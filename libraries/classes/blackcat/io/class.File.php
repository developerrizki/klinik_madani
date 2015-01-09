<?php
/**
 * BlackCat PHP 5 Framework
 *
 * Last updated: June 05, 2010, 11:16 PM
 *
 * @package   io
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 */

/**
 * IOException class
 */
require_once CLASS_DIR . '/blackcat/io/class.IOException.php';


/**
 * File handler class
 *
 * @package   io
 * @author    Lorensius W. L. T <lorenz@londatiga.net>
 * @version   1.0
 * @copyright Copyright (c) 2010 Lorensius W. L. T
 *
 */
class BCFile
{
    /**
     * File name
     *
     * @var string
     */
    private $_fileName = '';


    /**
     * Constructor.
     * Create a new instance of this class
     *
     * @param string $fileName File name (optional)
     *
     * @return void
     */
    public function __construct($fileName = '')
    {
        $this->_fileName = $fileName;
    }

    /**
     * Set file name
     *
     * @param string $fileName File name
     *
     * @return void
     */
    public function setFileName($fileName)
    {
        $this->_fileName = $fileName;
    }

    /**
     * Get file name
     *
     * @return string File name
     */
    public function getFileName()
    {
        return $this->_fileName;
    }

    /**
     * Get real path
     *
     * @return string Real path
     */
    public function getRealPath()
    {
        return realpath($this->_fileName);
    }

    /**
     * Get base name
     *
     * @return string Base name
     */
    public function getBaseName()
    {
        return basename($this->_fileName);
    }

    /**
     * Check if a file or directory exists
     *
     * @return bool TRUE if the file or directory exists; FALSE otherwise
     */
    public function exists()
    {
        return file_exists($this->_fileName);
    }

    /**
     * Check if a file exist or is a regular file
     *
     * @return bool TRUE if the file or directory exists and is a regular file; FALSE otherwise
     */
    public function isFile()
    {
        return is_file($this->_fileName);
    }

    /**
     * Check if a file is readable
     *
     * @return bool TRUE if is readable and vice versa
     */
    public function isReadable()
    {
        return is_readable($this->_fileName);
    }

    /**
     * Check if a file is writeable
     *
     * @return boolean TRUE if is writeable and vice versa
     */
    public function isWriteable()
    {
        return is_writeable($this->_fileName);
    }

    /**
     * Load a file
     *
     * @param bool $once TRUE if include_once or FALSE for include
     *
     * @throws IOException If file could not be loaded
     *
     * @return void
     */
    public function load($once = true)
    {
        if ($this->isReadable()) {
            if ($once) {
                include_once($this->_fileName);
            } else {
                include($this->_fileName);
            }
        } else {
            throw new IOException('File <i>' . $this->_fileName . '</i> could not be loaded!');
        }
    }

    /**
     * Rename a file
     *
     * @param string $newName New file name
     *
     * @throws IOException If file source doesn't exist or is not regular file
     *
     * @return boolean TRUE on success or FALSE on failure
     */
    public function rename($newName)
    {
        $res = false;

        if ($this->isFile()) {
            $res = @rename($this->_fileName, $newName);
        } else {
            throw new IOException('File <i>' . $this->_fileName . '</i> doesn\'t exist or is not regular file');
        }

        return $res;
    }

    /**
     * Delete a file
     *
     * @throws IOException If file source doesn't exist or is not regular file
     *
     * @return boolean TRUE on success or FALSE on failure
     */
    public function delete()
    {
        $res = false;

        if ($this->isFile()) {
            $res = @unlink($this->_fileName);
        } else {
            throw new IOException('File <i>' . $this->_fileName . '</i> doesn\'t exist or is not regular file');
        }

        return $res;
    }

    /**
     * Makes a copy of the file source to a destination
     *
     * @param string $destination Copy destination
     *
     * @throws IOException If file source doesn't exist or is not regular file
     *
     * @return TRUE on success or FALSE on failure
     */
    public function copy($destination)
    {
        $res = false;

        if ($this->isFile()) {
            $res = @copy($this->_fileName, $destination);
        } else {
            throw new IOException('File <i>' . $this->_fileName . '</i> doesn\'t exist or is not regular file');
        }

        return $res;
    }

    /**
     * Get file extension
     *
     * @return string File extension
     */
    public function getExtension()
    {
        $arr = explode('.', $this->_fileName);

        return $arr[sizeof($arr) - 1];
    }

    /**
     * Get file type
     *
     * @return string File type
     */
    public function getType()
    {
		$mimeTypes = array("ez" => "application/andrew-inset",
                           "hqx" => "application/mac-binhex40",
                           "cpt" => "application/mac-compactpro",
                           "doc" => "application/msword",
                           "bin" => "application/octet-stream",
                           "dms" => "application/octet-stream",
                           "lha" => "application/octet-stream",
                           "lzh" => "application/octet-stream",
                           "exe" => "application/octet-stream",
                           "class" => "application/octet-stream",
                           "so" => "application/octet-stream",
                           "dll" => "application/octet-stream",
                           "oda" => "application/oda",
                           "pdf" => "application/pdf",
                           "ai" => "application/postscript",
                           "eps" => "application/postscript",
                           "ps" => "application/postscript",
                           "smi" => "application/smil",
                           "smil" => "application/smil",
                           "wbxml" => "application/vndwapwbxml",
                           "wmlc" => "application/vndwapwmlc",
                           "wmlsc" => "application/vndwapwmlscriptc",
                           "bcpio" => "application/x-bcpio",
                           "vcd" => "application/x-cdlink",
                           "pgn" => "application/x-chess-pgn",
                           "cpio" => "application/x-cpio",
                           "csh" => "application/x-csh",
                           "dcr" => "application/x-director",
                           "dir" => "application/x-director",
                           "dxr" => "application/x-director",
                           "dvi" => "application/x-dvi",
                           "spl" => "application/x-futuresplash",
                           "gtar" => "application/x-gtar",
                           "hdf" => "application/x-hdf",
                           "js" => "application/x-javascript",
                           "skp" => "application/x-koan",
                           "skd" => "application/x-koan",
                           "skt" => "application/x-koan",
                           "skm" => "application/x-koan",
                           "latex" => "application/x-latex",
                           "nc" => "application/x-netcdf",
                           "cdf" => "application/x-netcdf",
                           "sh" => "application/x-sh",
                           "shar" => "application/x-shar",
                           "swf" => "application/x-shockwave-flash",
                           "sit" => "application/x-stuffit",
                           "sv4cpio" => "application/x-sv4cpio",
                           "sv4crc" => "application/x-sv4crc",
                           "tar" => "application/x-tar",
                           "tcl" => "application/x-tcl",
                           "tex" => "application/x-tex",
                           "texinfo" => "application/x-texinfo",
                           "texi" => "application/x-texinfo",
                           "t" => "application/x-troff",
                           "tr" => "application/x-troff",
                           "roff" => "application/x-troff",
                           "man" => "application/x-troff-man",
                           "me" => "application/x-troff-me",
                           "ms" => "application/x-troff-ms",
                           "ustar" => "application/x-ustar",
                           "src" => "application/x-wais-source",
                           "xhtml" => "application/xhtml+xml",
                           "xht" => "application/xhtml+xml",
                           "zip" => "application/zip",
                           "au" => "audio/basic",
                           "snd" => "audio/basic",
                           "mid" => "audio/midi",
                           "midi" => "audio/midi",
                           "kar" => "audio/midi",
                           "mpga" => "audio/mpeg",
                           "mp2" => "audio/mpeg",
                           "mp3" => "audio/mpeg",
                           "aif" => "audio/x-aiff",
                           "aiff" => "audio/x-aiff",
                           "aifc" => "audio/x-aiff",
                           "m3u" => "audio/x-mpegurl",
                           "ram" => "audio/x-pn-realaudio",
                           "rm" => "audio/x-pn-realaudio",
                           "rpm" => "audio/x-pn-realaudio-plugin",
                           "ra" => "audio/x-realaudio",
                           "wav" => "audio/x-wav",
                           "pdb" => "chemical/x-pdb",
                           "xyz" => "chemical/x-xyz",
                           "bmp" => "image/bmp",
                           "gif" => "image/gif",
                           "ief" => "image/ief",
                           "jpeg" => "image/jpeg",
                           "jpg" => "image/jpeg",
                           "jpe" => "image/jpeg",
                           "png" => "image/png",
                           "tiff" => "image/tiff",
                           "tif" => "image/tif",
                           "djvu" => "image/vnddjvu",
                           "djv" => "image/vnddjvu",
                           "wbmp" => "image/vndwapwbmp",
                           "ras" => "image/x-cmu-raster",
                           "pnm" => "image/x-portable-anymap",
                           "pbm" => "image/x-portable-bitmap",
                           "pgm" => "image/x-portable-graymap",
                           "ppm" => "image/x-portable-pixmap",
                           "rgb" => "image/x-rgb",
                           "xbm" => "image/x-xbitmap",
                           "xpm" => "image/x-xpixmap",
                           "xwd" => "image/x-windowdump",
                           "igs" => "model/iges",
                           "iges" => "model/iges",
                           "msh" => "model/mesh",
                           "mesh" => "model/mesh",
                           "silo" => "model/mesh",
                           "wrl" => "model/vrml",
                           "vrml" => "model/vrml",
                           "css" => "text/css",
                           "html" => "text/html",
                           "htm" => "text/html",
                           "asc" => "text/plain",
                           "txt" => "text/plain",
                           "rtx" => "text/richtext",
                           "rtf" => "text/rtf",
                           "sgml" => "text/sgml",
                           "sgm" => "text/sgml",
                           "tsv" => "text/tab-seperated-values",
                           "wml" => "text/vndwapwml",
                           "wmls" => "text/vndwapwmlscript",
                           "etx" => "text/x-setext",
                           "xml" => "text/xml",
                           "xsl" => "text/xml",
                           "mpeg" => "video/mpeg",
                           "mpg" => "video/mpeg",
                           "mpe" => "video/mpeg",
                           "qt" => "video/quicktime",
                           "mov" => "video/quicktime",
                           "mxu" => "video/vndmpegurl",
                           "avi" => "video/x-msvideo",
                           "movie" => "video/x-sgi-movie",
                           "ice" => "x-conference-xcooltalk");

        $ext      = strtolower($this->getExtension($this->_fileName));
        $mimeType = 'application/force-download';

        if (array_key_exists($ext, $mimeTypes)) {
            $mimeType = $mimeTypes[$ext];
        } else {
            if (function_exists('mime_content_type')) {
                $mipeType = mime_content_type($this->_fileName);
            } else if (function_exists('finfo_file')) {
                $finfo = finfo_open(FILEINFO_MIME); // return mime type
                $mtype = finfo_file($finfo, $this->_fileName);
                finfo_close($finfo);
            }
        }

        return $mimeType;
    }

    /**
     * Write a file
     *
     * @param mixed $data Data to be writen into file
     * @param string $mode Write mode (w, wb, a, etc)
     *
     * @throws IOException If problem occured
     *
     * @return void
     */
    public function write($data, $mode = 'w')
    {
        if (!$this->exists()) { //file not already exists
            $fh = @fopen($this->_fileName, $mode);

            if ($fh) {
                fputs($fh, $data);

                @fclose($fh);
            } else {
                throw new IOException('Can not write file <i>' . basename($this->_fileName) . '</i>!');
            }
        } else {
            if ($this->isWriteable()) {
                $fh = @fopen($this->_fileName, $mode);

                if ($fh) {
                    fputs($fh, $data);

                    @fclose($fh);
                }
            } else {
                throw new IOException('File <i>' . basename($this->_fileName) . ' is not writeable</i>!');
            }
        }
    }

    /**
     * Read a file
     *
     * @return string File contents
     */
    public function read()
    {
        $contents = '';

        if ($this->exists()) {
            $fh = @fopen($this->_fileName, 'r');

            if ($fh) {
                $contents = @fread($fh, filesize($this->_fileName));

                @fclose($fh);
            } else {
                throw new IOException('Can not read file <i>' . basename($this->_fileName) . '</i>!');
            }
        } else {
            throw new IOException('File <i>' . basename($this->_fileName) . ' </i> not found!');
        }

        return $contents;
    }

    /**
     * Download a file
     * Get file stream and send it browser for client download purposes
     *
     * @throw IOException If problem occured
     *
     * @return void
     */
    public function download()
    {
        if ($this->exists()) {
            $mimeType = $this->getType();
            $fname    = basename($this->_fileName);

            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Type: $mimeType");
            header("Content-Disposition: attachment; filename=\"$fname\"");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . filesize($this->_fileName));

            $file = @fopen($this->_fileName, 'rb');

            if ($file) {
                while(!feof($file)) {
                    print(fread($file, 1024*8));
                    flush();
                    if (connection_status() != 0) {
                        @fclose($file);
                        die();
                    }
                }

                @fclose($file);
            }
        } else {
            throw new IOException('File <i>' . basename($this->_fileName) . '</i> doesnt exist!');
        }
    }
}