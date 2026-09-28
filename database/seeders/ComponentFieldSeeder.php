<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Component;
use App\Models\ComponentField;
use App\Models\SectionComponentData;

class ComponentFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Button Component
        $button = Component::where('component_name', 'Button')->orWhere('component_slug', 'button')->first();
        if ($button) {
            $f1 = ComponentField::firstOrCreate(
                ['component_id' => $button->id, 'field_name' => 'button_text'],
                [
                    'field_label'   => 'Button Text',
                    'field_type'    => 'text',
                    'placeholder'   => 'e.g. Apply Now / Click Here',
                    'default_value' => 'Click Here',
                    'help_text'     => 'The text displayed on the button',
                    'is_required'   => true,
                    'is_active'     => true,
                    'sort_order'    => 1,
                ]
            );

            $f2 = ComponentField::firstOrCreate(
                ['component_id' => $button->id, 'field_name' => 'button_url'],
                [
                    'field_label'   => 'Button URL',
                    'field_type'    => 'url',
                    'placeholder'   => 'https://example.com or #contact',
                    'default_value' => '#',
                    'help_text'     => 'Target link or URL for the button',
                    'is_required'   => true,
                    'is_active'     => true,
                    'sort_order'    => 2,
                ]
            );

            $f3 = ComponentField::firstOrCreate(
                ['component_id' => $button->id, 'field_name' => 'target'],
                [
                    'field_label'   => 'Target',
                    'field_type'    => 'select',
                    'placeholder'   => '-- Select Target Window --',
                    'default_value' => 'same',
                    'help_text'     => 'How the link should open',
                    'is_required'   => false,
                    'is_active'     => true,
                    'sort_order'    => 3,
                    'options'       => [
                        ['value' => 'same', 'label' => 'Same Tab'],
                        ['value' => 'blank', 'label' => 'New Tab'],
                    ],
                ]
            );

            // Migrate legacy data for button: content_value -> button_text, extra_value -> button_url
            $legacyButtons = SectionComponentData::where('component_id', $button->id)
                ->whereNull('component_field_id')
                ->get();

            foreach ($legacyButtons as $btnData) {
                if ($btnData->content_value) {
                    SectionComponentData::firstOrCreate(
                        [
                            'section_id'         => $btnData->section_id,
                            'component_id'       => $button->id,
                            'sub_component_id'   => $btnData->sub_component_id,
                            'component_field_id' => $f1->id,
                        ],
                        [
                            'field_name'    => 'button_text',
                            'content_value' => $btnData->content_value,
                        ]
                    );
                }

                if ($btnData->extra_value) {
                    SectionComponentData::firstOrCreate(
                        [
                            'section_id'         => $btnData->section_id,
                            'component_id'       => $button->id,
                            'sub_component_id'   => $btnData->sub_component_id,
                            'component_field_id' => $f2->id,
                        ],
                        [
                            'field_name'    => 'button_url',
                            'content_value' => $btnData->extra_value,
                        ]
                    );
                }
            }
        }

        // 2. Heading Component
        $heading = Component::where('component_name', 'Heading')->orWhere('component_slug', 'heading')->first();
        if ($heading) {
            $fHeading = ComponentField::firstOrCreate(
                ['component_id' => $heading->id, 'field_name' => 'heading'],
                [
                    'field_label'   => 'Heading',
                    'field_type'    => 'text',
                    'placeholder'   => 'Enter heading title...',
                    'default_value' => null,
                    'help_text'     => 'Main heading title',
                    'is_required'   => true,
                    'is_active'     => true,
                    'sort_order'    => 1,
                ]
            );

            // Migrate legacy heading data
            $legacyHeadings = SectionComponentData::where('component_id', $heading->id)
                ->whereNull('component_field_id')
                ->get();
            foreach ($legacyHeadings as $hdData) {
                if ($hdData->content_value) {
                    SectionComponentData::firstOrCreate(
                        [
                            'section_id'         => $hdData->section_id,
                            'component_id'       => $heading->id,
                            'sub_component_id'   => $hdData->sub_component_id,
                            'component_field_id' => $fHeading->id,
                        ],
                        [
                            'field_name'    => 'heading',
                            'content_value' => $hdData->content_value,
                        ]
                    );
                }
            }
        }

        // 3. SubHeading Component
        $subHeading = Component::where('component_name', 'SubHeading')->orWhere('component_slug', 'subheading')->first();
        if ($subHeading) {
            $fSubHeading = ComponentField::firstOrCreate(
                ['component_id' => $subHeading->id, 'field_name' => 'subheading'],
                [
                    'field_label'   => 'SubHeading',
                    'field_type'    => 'text',
                    'placeholder'   => 'Enter subheading text...',
                    'default_value' => null,
                    'help_text'     => 'Supporting subheading text',
                    'is_required'   => false,
                    'is_active'     => true,
                    'sort_order'    => 1,
                ]
            );

            // Migrate legacy subheading data
            $legacySubHeadings = SectionComponentData::where('component_id', $subHeading->id)
                ->whereNull('component_field_id')
                ->get();
            foreach ($legacySubHeadings as $shData) {
                if ($shData->content_value) {
                    SectionComponentData::firstOrCreate(
                        [
                            'section_id'         => $shData->section_id,
                            'component_id'       => $subHeading->id,
                            'sub_component_id'   => $shData->sub_component_id,
                            'component_field_id' => $fSubHeading->id,
                        ],
                        [
                            'field_name'    => 'subheading',
                            'content_value' => $shData->content_value,
                        ]
                    );
                }
            }
        }

        // 4. Paragraph / Description Component
        $paragraph = Component::where('component_name', 'Paragraph')->orWhere('component_slug', 'paragraph')->first();
        if ($paragraph) {
            $fDesc = ComponentField::firstOrCreate(
                ['component_id' => $paragraph->id, 'field_name' => 'description'],
                [
                    'field_label'   => 'Description',
                    'field_type'    => 'textarea',
                    'placeholder'   => 'Enter paragraph description or content...',
                    'default_value' => null,
                    'help_text'     => 'Detailed description paragraph',
                    'is_required'   => false,
                    'is_active'     => true,
                    'sort_order'    => 1,
                ]
            );

            // Migrate legacy paragraph data
            $legacyParas = SectionComponentData::where('component_id', $paragraph->id)
                ->whereNull('component_field_id')
                ->get();
            foreach ($legacyParas as $pData) {
                if ($pData->content_value) {
                    SectionComponentData::firstOrCreate(
                        [
                            'section_id'         => $pData->section_id,
                            'component_id'       => $paragraph->id,
                            'sub_component_id'   => $pData->sub_component_id,
                            'component_field_id' => $fDesc->id,
                        ],
                        [
                            'field_name'    => 'description',
                            'content_value' => $pData->content_value,
                        ]
                    );
                }
            }
        }

        // 5. Image Component
        $image = Component::where('component_name', 'Image')->orWhere('component_slug', 'image')->first();
        if ($image) {
            $fImage = ComponentField::firstOrCreate(
                ['component_id' => $image->id, 'field_name' => 'image'],
                [
                    'field_label'   => 'Image',
                    'field_type'    => 'image',
                    'placeholder'   => 'Upload image file',
                    'default_value' => null,
                    'help_text'     => 'Component image file (PNG, JPG, WEBP)',
                    'is_required'   => false,
                    'is_active'     => true,
                    'sort_order'    => 1,
                ]
            );

            // Migrate legacy image data
            $legacyImages = SectionComponentData::where('component_id', $image->id)
                ->whereNull('component_field_id')
                ->get();
            foreach ($legacyImages as $imData) {
                if ($imData->file_path) {
                    SectionComponentData::firstOrCreate(
                        [
                            'section_id'         => $imData->section_id,
                            'component_id'       => $image->id,
                            'sub_component_id'   => $imData->sub_component_id,
                            'component_field_id' => $fImage->id,
                        ],
                        [
                            'field_name'    => 'image',
                            'file_path'     => $imData->file_path,
                            'content_value' => $imData->file_path,
                        ]
                    );
                }
            }
        }

        // 6. Video Component
        $video = Component::where('component_name', 'Video')->orWhere('component_slug', 'video')->first();
        if ($video) {
            $fVideo = ComponentField::firstOrCreate(
                ['component_id' => $video->id, 'field_name' => 'video'],
                [
                    'field_label'   => 'Video',
                    'field_type'    => 'video',
                    'placeholder'   => 'Upload video file',
                    'default_value' => null,
                    'help_text'     => 'Component video file (MP4, WEBM)',
                    'is_required'   => false,
                    'is_active'     => true,
                    'sort_order'    => 1,
                ]
            );

            // Migrate legacy video data
            $legacyVideos = SectionComponentData::where('component_id', $video->id)
                ->whereNull('component_field_id')
                ->get();
            foreach ($legacyVideos as $vidData) {
                if ($vidData->file_path) {
                    SectionComponentData::firstOrCreate(
                        [
                            'section_id'         => $vidData->section_id,
                            'component_id'       => $video->id,
                            'sub_component_id'   => $vidData->sub_component_id,
                            'component_field_id' => $fVideo->id,
                        ],
                        [
                            'field_name'    => 'video',
                            'file_path'     => $vidData->file_path,
                            'content_value' => $vidData->file_path,
                        ]
                    );
                }
            }
        }

        // 7. Anchor Component (<a> tag)
        $anchor = Component::where('component_name', 'Anchor')->orWhere('component_slug', 'anchor')->first();
        if ($anchor) {
            $fAnchorText = ComponentField::firstOrCreate(
                ['component_id' => $anchor->id, 'field_name' => 'anchor_text'],
                [
                    'field_label'   => 'Anchor Text',
                    'field_type'    => 'text',
                    'placeholder'   => 'e.g. Learn More / Read More',
                    'default_value' => 'Learn More',
                    'help_text'     => 'The visible text for the anchor link',
                    'is_required'   => true,
                    'is_active'     => true,
                    'sort_order'    => 1,
                ]
            );

            $fAnchorUrl = ComponentField::firstOrCreate(
                ['component_id' => $anchor->id, 'field_name' => 'anchor_url'],
                [
                    'field_label'   => 'Anchor URL (href)',
                    'field_type'    => 'url',
                    'placeholder'   => 'https://example.com, /page, or #section',
                    'default_value' => '#',
                    'help_text'     => 'Destination link or target URL (href)',
                    'is_required'   => true,
                    'is_active'     => true,
                    'sort_order'    => 2,
                ]
            );

            $fAnchorTarget = ComponentField::firstOrCreate(
                ['component_id' => $anchor->id, 'field_name' => 'target'],
                [
                    'field_label'   => 'Target Window',
                    'field_type'    => 'select',
                    'placeholder'   => '-- Select Target Window --',
                    'default_value' => '_self',
                    'help_text'     => 'Where to open the link',
                    'is_required'   => false,
                    'is_active'     => true,
                    'sort_order'    => 3,
                    'options'       => [
                        ['value' => '_self', 'label' => 'Same Tab / Window (_self)'],
                        ['value' => '_blank', 'label' => 'New Tab (_blank)'],
                    ],
                ]
            );

            $fAnchorTitle = ComponentField::firstOrCreate(
                ['component_id' => $anchor->id, 'field_name' => 'title'],
                [
                    'field_label'   => 'Title / Tooltip',
                    'field_type'    => 'text',
                    'placeholder'   => 'e.g. Visit documentation or help section',
                    'default_value' => null,
                    'help_text'     => 'Optional hover text for accessibility (title attribute)',
                    'is_required'   => false,
                    'is_active'     => true,
                    'sort_order'    => 4,
                ]
            );

            $fAnchorRel = ComponentField::firstOrCreate(
                ['component_id' => $anchor->id, 'field_name' => 'rel'],
                [
                    'field_label'   => 'Relationship (rel)',
                    'field_type'    => 'select',
                    'placeholder'   => '-- Select Link Relationship --',
                    'default_value' => 'none',
                    'help_text'     => 'Relationship of target URL (e.g. noopener, nofollow)',
                    'is_required'   => false,
                    'is_active'     => true,
                    'sort_order'    => 5,
                    'options'       => [
                        ['value' => 'none', 'label' => 'Standard Link (None)'],
                        ['value' => 'noopener noreferrer', 'label' => 'noopener noreferrer (Secure External Link)'],
                        ['value' => 'nofollow', 'label' => 'nofollow (Search Engines)'],
                        ['value' => 'noopener noreferrer nofollow', 'label' => 'nofollow + noopener noreferrer'],
                    ],
                ]
            );

            // Migrate legacy anchor data if any exists
            $legacyAnchors = SectionComponentData::where('component_id', $anchor->id)
                ->whereNull('component_field_id')
                ->get();
            foreach ($legacyAnchors as $ancData) {
                if ($ancData->content_value) {
                    SectionComponentData::firstOrCreate(
                        [
                            'section_id'         => $ancData->section_id,
                            'component_id'       => $anchor->id,
                            'sub_component_id'   => $ancData->sub_component_id,
                            'component_field_id' => $fAnchorText->id,
                        ],
                        [
                            'field_name'    => 'anchor_text',
                            'content_value' => $ancData->content_value,
                        ]
                    );
                }
                if ($ancData->extra_value) {
                    SectionComponentData::firstOrCreate(
                        [
                            'section_id'         => $ancData->section_id,
                            'component_id'       => $anchor->id,
                            'sub_component_id'   => $ancData->sub_component_id,
                            'component_field_id' => $fAnchorUrl->id,
                        ],
                        [
                            'field_name'    => 'anchor_url',
                            'content_value' => $ancData->extra_value,
                        ]
                    );
                }
            }
        }
    }
}
