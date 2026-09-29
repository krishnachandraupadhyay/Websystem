<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\SectionComponentData;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    /**
     * Display the main frontend homepage with dynamic section data.
     */
    public function index()
    {
        $headerData = $this->getHeaderData();
        $placementData = $this->getPlacementData();

        return view('frontend.index', compact('headerData', 'placementData'));
    }

    /**
     * Fetch and structure configured data for Section 4 (Campus Placement).
     */
    protected function getPlacementData()
    {
        $placementSection = Section::with(['components' => function($q) {
            $q->wherePivot('status', 1)->with(['fields', 'subcomponents.fields']);
        }])->where(function($q) {
            $q->where('id', 4)->orWhere('section_slug', 'campus-placement');
        })->first();

        if (!$placementSection) {
            return [];
        }

        $allData = SectionComponentData::where('section_id', $placementSection->id)
            ->orderBy('instance_index', 'asc')
            ->get();

        $placementData = [
            'heading'    => null,
            'subheading' => null,
            'paragraph'  => null,
            'cards'      => [],
        ];

        // 1. Process top-level section Heading, SubHeading, Paragraph
        foreach ($allData->whereNull('sub_component_id') as $item) {
            $comp = $placementSection->components->firstWhere('id', $item->component_id);
            if (!$comp) continue;

            $slug = strtolower($comp->component_slug);
            if (str_contains($slug, 'subheading')) {
                if (!empty($item->content_value)) $placementData['subheading'] = $item->content_value;
            } elseif (str_contains($slug, 'heading')) {
                if (!empty($item->content_value)) $placementData['heading'] = $item->content_value;
            } elseif (str_contains($slug, 'paragraph') || str_contains($slug, 'desc')) {
                if (!empty($item->content_value)) $placementData['paragraph'] = $item->content_value;
            }
        }

        // 2. Process Card Container (component 8) instances
        $cardComp = $placementSection->components->first(function($c) {
            return str_contains(strtolower($c->component_slug), 'card') || $c->id === 8;
        });

        if ($cardComp) {
            $cardItems = $allData->where('component_id', $cardComp->id)->groupBy('instance_index');
            foreach ($cardItems as $idx => $records) {
                $card = [
                    'name'        => null,
                    'role'        => null,
                    'image'       => null,
                    'description' => null,
                    'button_text' => 'View Placement Story',
                    'button_url'  => '#contact',
                    'target'      => '_self',
                ];

                foreach ($records as $r) {
                    $fieldName = strtolower($r->field_name ?? '');
                    if ($r->file_path && ($fieldName === 'image' || str_contains($fieldName, 'photo') || str_contains($fieldName, 'image'))) {
                        $card['image'] = $r->file_path;
                    } elseif ($fieldName === 'subheading' || str_contains($fieldName, 'sub')) {
                        if (!empty($r->content_value)) $card['role'] = $r->content_value;
                    } elseif ($fieldName === 'heading' || str_contains($fieldName, 'heading')) {
                        if (!empty($r->content_value)) $card['name'] = $r->content_value;
                    } elseif ($fieldName === 'description' || str_contains($fieldName, 'desc') || str_contains($fieldName, 'paragraph')) {
                        if (!empty($r->content_value)) $card['description'] = $r->content_value;
                    } elseif (in_array($fieldName, ['button_text', 'text', 'label'], true)) {
                        if (!empty($r->content_value)) $card['button_text'] = $r->content_value;
                    } elseif (in_array($fieldName, ['button_url', 'url', 'link', 'href'], true)) {
                        if (!empty($r->content_value)) $card['button_url'] = $r->content_value;
                    } elseif ($fieldName === 'target') {
                        if (!empty($r->content_value)) $card['target'] = $r->content_value;
                    }
                }

                if (!empty($card['name']) || !empty($card['role']) || !empty($card['image'])) {
                    $placementData['cards'][] = $card;
                }
            }
        }

        return $placementData;
    }

    /**
     * Fetch and structure configured data for Section 1 (Header).
     */
    protected function getHeaderData()
    {
        $headerSection = Section::with(['components' => function($q) {
            $q->wherePivot('status', 1)->with(['fields', 'subcomponents.fields']);
        }])->where(function($q) {
            $q->where('id', 1)->orWhere('section_slug', 'header');
        })->first();

        if (!$headerSection) {
            return [];
        }

        $allData = SectionComponentData::where('section_id', $headerSection->id)
            ->orderBy('instance_index', 'asc')
            ->get();

        $headerData = [
            'logo'       => null,
            'heading'    => null,
            'subheading' => null,
            'nav_links'  => [],
            'buttons'    => [],
        ];

        // 1. Process Logo and Headings
        foreach ($allData as $item) {
            $comp = $headerSection->components->firstWhere('id', $item->component_id);
            if (!$comp) continue;

            $slug = strtolower($comp->component_slug);

            // Logo
            if (str_contains($slug, 'image') || str_contains($slug, 'logo')) {
                if ($item->file_path && file_exists(public_path($item->file_path))) {
                    $headerData['logo'] = $item->file_path;
                }
            }

            // Headings
            if (str_contains($slug, 'subheading')) {
                if (!empty($item->content_value)) {
                    $headerData['subheading'] = $item->content_value;
                }
            } elseif (str_contains($slug, 'heading') || str_contains($slug, 'title')) {
                if (!empty($item->content_value)) {
                    $headerData['heading'] = $item->content_value;
                }
            }
        }

        // 2. Process Navigation Links (Nav component with Anchor subcomponents)
        $navComp = $headerSection->components->first(function($c) {
            return str_contains(strtolower($c->component_slug), 'nav') || str_contains(strtolower($c->component_slug), 'menu');
        });

        if ($navComp) {
            $navItems = $allData->where('component_id', $navComp->id)->groupBy('instance_index');
            foreach ($navItems as $idx => $records) {
                $linkText = null;
                $linkUrl = '#';
                $target = '_self';
                $title = '';

                foreach ($records as $r) {
                    $fieldName = strtolower($r->field_name ?? '');
                    if (str_contains($fieldName, 'text') || str_contains($fieldName, 'label')) {
                        $linkText = $r->content_value;
                    } elseif (str_contains($fieldName, 'url') || str_contains($fieldName, 'href') || str_contains($fieldName, 'link')) {
                        $linkUrl = $r->content_value ?: '#';
                    } elseif (str_contains($fieldName, 'target')) {
                        $target = $r->content_value ?: '_self';
                    } elseif (str_contains($fieldName, 'tooltip') || str_contains($fieldName, 'title')) {
                        $title = $r->content_value ?: '';
                    }

                    // Fallbacks for legacy content
                    if (!$linkText && $r->content_value && !str_contains($fieldName, 'url') && !str_contains($fieldName, 'target')) {
                        $linkText = $r->content_value;
                    }
                    if ($r->extra_value && $linkUrl === '#') {
                        $linkUrl = $r->extra_value;
                    }
                }

                if (!empty($linkText)) {
                    $headerData['nav_links'][] = [
                        'text'   => $linkText,
                        'url'    => $linkUrl,
                        'target' => $target,
                        'title'  => $title,
                    ];
                }
            }
        }

        // 3. Process Header Action Buttons
        $btnComp = $headerSection->components->first(function($c) {
            return str_contains(strtolower($c->component_slug), 'button') || str_contains(strtolower($c->component_slug), 'btn');
        });

        if ($btnComp) {
            $btnItems = $allData->where('component_id', $btnComp->id)->groupBy('instance_index');
            foreach ($btnItems as $idx => $records) {
                $btnText = null;
                $btnUrl = '#';
                $target = '_self';

                foreach ($records as $r) {
                    $fieldName = strtolower($r->field_name ?? '');
                    if (str_contains($fieldName, 'text') || str_contains($fieldName, 'label')) {
                        $btnText = $r->content_value;
                    } elseif (str_contains($fieldName, 'url') || str_contains($fieldName, 'href') || str_contains($fieldName, 'link')) {
                        $btnUrl = $r->content_value ?: '#';
                    } elseif (str_contains($fieldName, 'target')) {
                        $target = $r->content_value ?: '_self';
                    }

                    // Fallbacks for legacy content
                    if (!$btnText && $r->content_value && !str_contains($fieldName, 'url') && !str_contains($fieldName, 'target')) {
                        $btnText = $r->content_value;
                    }
                    if ($r->extra_value && $btnUrl === '#') {
                        $btnUrl = $r->extra_value;
                    }
                }

                if (!empty($btnText)) {
                    $headerData['buttons'][] = [
                        'text'   => $btnText,
                        'url'    => $btnUrl,
                        'target' => $target,
                    ];
                }
            }
        }

        return $headerData;
    }
}
