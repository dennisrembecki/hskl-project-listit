<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class AppExtension extends AbstractExtension
{

    public function __construct(Container $container)
    {

    }

    public function getFilters()
    {
        return array(
            new TwigFilter('urlfilter', array($this, 'urlfilter')),
            new TwigFilter('category', array($this, 'category')),
        );
    }

    public function urlfilter($string)
    {

        $new_string = preg_replace("(^https?://)", "", $string);
        $newer_string = preg_replace("(^www.)", "", $new_string);
        $newest_string = rtrim($newer_string,"/");

        return $newest_string;
    }

    public function category($type)
    {

        if ($type == "websites") {
            return '<i class="fa-solid fa-laptop"></i>'. " Webseiten";
        }
        if ($type == "movies") {
            return '<i class="fa-solid fa-video"></i>'." Filme" ;
        }
        if ($type == "streaming") {
            return '<i class="fa-solid fa-link"></i>'." Streamen";
        }
        if ($type == "series") {
            return '<i class="fa-solid fa-video"></i>'." Serien/Shows";
        }
        if ($type == "games") {
        return '<i class="fa-solid fa-gamepad"></i>'." Spiele";
         }
        if ($type == "other") {
            return '<i class="fa-solid fa-folder-tree"></i>'." Sonstiges";
        }

        return ucfirst($type);

            //   <option value="streaming_movie">Filme</option>
            //                            <option value="streaming_series">Serien</option>
            //                            <option value="streaming_show">Shows</option>
            //                            <option value="streaming_video">Videos</option>
            //                            <option value="streaming_music">Musik</option>
            //                            <option value="streaming_podcasts">Hörbücher/Podcasts</option>
            //                            <option value="websites_buy">Einkaufen</option>
            //                            <option value="websites_science">Wissenschaft</option>
            //                            <option value="websites_literature">Literatur</option>
            //                            <option value="websites_entertainment">Unterhaltung</option>
            //                            <option value="websites">Sonstige Webseiten</option>
            //                        </optgroup>
            //                        <optgroup label="Dinge/ Gegenstände">
            //                            <option value="things_nutrition">Ernährung</option>
            //                            <option value="things_devices">Elektronik/Geräte</option>
            //                            <option value="things_books">Bücher</option>
            //                            <option value="things_games">Spiele</option>
            //                            <option value="things_software">Software/Apps</option>
            //                            <option value="things_beauty">Schönheit</option>
            //                            <option value="things_clothing">Kleidung/Taschen/Schmuck</option>
            //                            <option value="things_household">Küche/Haushalt/Wohnen</option>
            //                            <option value="things_leisure">Sport/Freizeit/Garten</option>
            //                            <option value="things_photo">Kamera/Foto</option>
            //                            <option value="things_work">Büro/Gewerbe/Industrie</option>
            //                            <option value="things_mobility">Mobilität</option>
            //                            <option value="things_science">Wissenschaft</option>
            //                            <option value="things_animals">Tiere</option>
            //                            <option value="things_presents">Geschenke</option>
            //                            <option value="things">Sonstige Dinge</option>
            //                        </optgroup>
            //                        <optgroup label="Sonstiges">
            //                            <option value="other" selected>Sonstiges</option>

    }
}