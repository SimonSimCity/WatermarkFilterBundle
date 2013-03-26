<?php

namespace Simonsimcity\LiipImagine\WatermarkFilterBundle\Imagine\Filter\Loader;


use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Point;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;

class WatermarkFilterLoader implements LoaderInterface
{
    private $imagine;

    public function __construct(ImagineInterface $imagine)
    {
        $this->imagine = $imagine;
    }

    /**
     * {@inheritDoc}
     */
    function load(ImageInterface $image, array $options = array())
    {
        $watermark = $this->imagine->open($options['file']);

        $position = (isset($options['placement']['position'])) ? $options['placement']['position'] : "center";

        // Stretched is the only currently supported form. All else does not change the size of the image. It does not keep the image-radio.
        $resize = (isset($options['placement']['resize'])) ? $options['placement']['resize'] : "";

        // You can use pixel (without unit) or percentage by adding the % sign.
        $space = (isset($options['placement']['space'])) ? $options['placement']['space'] : "0";

        $size = $image->getSize();
        $wSize = $watermark->getSize();

        // Calculate the space of the watermark for X and Y axis
        if (substr($space, -1) === "%") {
            $spacePercentage = (substr($space, 0, -1) / 100);

            $spaceX = (int)($size->getWidth() * $spacePercentage);
            $spaceY = (int)($size->getHeight() * $spacePercentage);
        } else {
            $spaceX = (int)$space;
            $spaceY = (int)$space;
        }

        // Resize the watermark relative to 100% of the image if "stretched" is used, subtracting the spaces :)
        if ($resize === "stretch") {
            $wWidth = $size->getWidth() - $spaceX * 2;
            $wHeight = $size->getHeight() - $spaceY * 2;

            /** @var $watermark ImageInterface */
            $watermark = $watermark->resize(new Box($wWidth, $wHeight));
            $wSize = $watermark->getSize();
        }
        // TODO: Add options like "fit", where the image is stretched, but the image-radio is kept ...
        // TODO: Or another option, where the watermark is resized, if it's too big for the image, not always.

        switch ($position) {
            case "leftTop":
                $x = $spaceX;
                $y = $spaceY;
                break;

            case "centerTop":
                $x = ($size->getWidth()/2) - ($wSize->getWidth()/2);
                $y = $spaceY;
                break;

            case "rightTop":
                $x = $size->getWidth() - $wSize->getWidth() - $spaceX;
                $y = $spaceY;
                break;

            case "leftCenter":
                $x = $spaceX;
                $y = ($size->getHeight()/2) - ($wSize->getHeight()/2);
                break;

            case "center":
                $x = ($size->getWidth()/2) - ($wSize->getWidth()/2);
                $y = ($size->getHeight()/2) - ($wSize->getHeight()/2);
                break;

            case "rightCenter":
                $x = $size->getWidth() - $wSize->getWidth() - $spaceX;
                $y = ($size->getHeight()/2) - ($wSize->getHeight()/2);
                break;

            case "leftBottom":
                $x = $spaceX;
                $y = $size->getHeight() - $wSize->getHeight() - $spaceY;
                break;

            case "centerBottom":
                $x = ($size->getWidth()/2) - ($wSize->getWidth()/2);
                $y = $size->getHeight() - $wSize->getHeight() - $spaceY;
                break;

            case "rightBottom":
                $x = $size->getWidth() - $wSize->getWidth() - $spaceX;
                $y = $size->getHeight() - $wSize->getHeight() - $spaceY;
                break;

            // Should never happen, but just in case ;)
            default:
                $x = 0;
                $y = 0;
        }

        $image->paste($watermark, new Point($x, $y));

        return $image;
    }
}