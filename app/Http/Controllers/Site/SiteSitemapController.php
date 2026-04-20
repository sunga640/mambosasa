<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class SiteSitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = [
            route('site.home'),
            route('site.booking'),
            route('site.cart'),
            route('site.page', ['slug' => 'about']),
            route('site.page', ['slug' => 'contact']),
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($urls as $loc) {
            $xml .= '  <url><loc>'.htmlspecialchars($loc, ENT_XML1 | ENT_QUOTES, 'UTF-8').'</loc><changefreq>weekly</changefreq></url>'."\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
