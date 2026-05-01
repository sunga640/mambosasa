<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSystemSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['nullable', 'string', 'max:255'],
            'address_line' => ['nullable', 'string', 'max:2000'],
            'phone' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'copyright_text' => ['nullable', 'string', 'max:500'],
            'facebook_url' => ['nullable', 'string', 'max:500'],
            'twitter_url' => ['nullable', 'string', 'max:500'],
            'instagram_url' => ['nullable', 'string', 'max:500'],
            'linkedin_url' => ['nullable', 'string', 'max:500'],
            'logo_header' => ['nullable', 'file', 'max:2048', 'extensions:jpg,jpeg,png,gif,svg,webp,avif'],
            'logo_footer' => ['nullable', 'file', 'max:2048', 'extensions:jpg,jpeg,png,gif,svg,webp,avif'],
            'hero_home_background' => ['nullable', 'file', 'max:5120', 'extensions:jpg,jpeg,png,gif,webp,avif'],
            'hero_home_slide_two' => ['nullable', 'file', 'max:5120', 'extensions:jpg,jpeg,png,gif,webp,avif'],
            'hero_home_slide_three' => ['nullable', 'file', 'max:5120', 'extensions:jpg,jpeg,png,gif,webp,avif'],
            'auth_guest_image' => ['nullable', 'file', 'max:5120', 'extensions:jpg,jpeg,png,gif,webp,avif'],
            'logo_header_media_asset_id' => ['nullable', 'integer', 'exists:media_assets,id'],
            'logo_footer_media_asset_id' => ['nullable', 'integer', 'exists:media_assets,id'],
            'hero_home_media_asset_id' => ['nullable', 'integer', 'exists:media_assets,id'],
            'hero_home_slide_two_media_asset_id' => ['nullable', 'integer', 'exists:media_assets,id'],
            'hero_home_slide_three_media_asset_id' => ['nullable', 'integer', 'exists:media_assets,id'],
            'auth_guest_image_media_asset_id' => ['nullable', 'integer', 'exists:media_assets,id'],
            'home_hero_gallery_media_asset_ids' => ['nullable', 'array', 'max:20'],
            'home_hero_gallery_media_asset_ids.*' => ['nullable', 'integer', 'exists:media_assets,id'],
            'home_views_gallery_media_asset_ids' => ['nullable', 'array', 'max:30'],
            'home_views_gallery_media_asset_ids.*' => ['nullable', 'integer', 'exists:media_assets,id'],
            'about_gallery_media_asset_ids' => ['nullable', 'array', 'max:20'],
            'about_gallery_media_asset_ids.*' => ['nullable', 'integer', 'exists:media_assets,id'],
            'hero_home_image_url' => ['nullable', 'string', 'max:2000', 'regex:/^https:\/\/.+/i'],
            'inner_page_hero_image_url' => ['nullable', 'string', 'max:2000', 'regex:/^https:\/\/.+/i'],
            'booking_payment_timeout_minutes' => ['nullable', 'integer', 'min:5', 'max:10080'],
            'booking_checkout_time' => ['nullable', 'date_format:H:i'],
            'booking_checkout_weekend_time' => ['nullable', 'date_format:H:i'],
            'smtp_host' => ['nullable', 'string', 'max:255'],
            'smtp_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'smtp_username' => ['nullable', 'string', 'max:255'],
            'smtp_password' => ['nullable', 'string', 'max:255'],
            'smtp_encryption' => ['nullable', 'in:tls,ssl,null'],
            'mail_from_address' => ['nullable', 'email', 'max:255'],
            'mail_from_name' => ['nullable', 'string', 'max:255'],
            'email_templates' => ['nullable', 'array'],
            'email_templates.*.enabled' => ['nullable', 'boolean'],
            'email_templates.*.details_enabled' => ['nullable', 'boolean'],
            'email_templates.*.accent_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'email_templates.*.subject' => ['nullable', 'string', 'max:255'],
            'email_templates.*.title' => ['nullable', 'string', 'max:255'],
            'email_templates.*.intro' => ['nullable', 'string', 'max:1000'],
            'email_templates.*.body' => ['nullable', 'string', 'max:5000'],
            'email_templates.*.highlight' => ['nullable', 'string', 'max:1000'],
            'email_templates.*.primary_button_label' => ['nullable', 'string', 'max:120'],
            'email_templates.*.secondary_button_label' => ['nullable', 'string', 'max:120'],
            'email_templates.*.footer_note' => ['nullable', 'string', 'max:1500'],
            'dashboard_theme_mode' => ['nullable', 'in:light,dark,system'],
            'restaurant_integration_enabled' => ['nullable', 'boolean'],
            'restaurant_api_base_url' => ['nullable', 'url', 'max:255'],
            'restaurant_api_key' => ['nullable', 'string', 'max:2000'],
            'restaurant_api_secret' => ['nullable', 'string', 'max:2000'],
            'restaurant_sso_shared_secret' => ['nullable', 'string', 'max:2000'],
            'restaurant_sso_entry_path' => ['nullable', 'string', 'max:255'],
            'restaurant_api_timeout_seconds' => ['nullable', 'integer', 'min:5', 'max:120'],
            'restaurant_token_ttl_minutes' => ['nullable', 'integer', 'min:1', 'max:120'],
            'stat_pools_count' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'stat_restaurants_count' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'home_stat_customers_label' => ['nullable', 'string', 'max:32'],
            'home_hero_eyebrow' => ['nullable', 'string', 'max:160'],
            'home_hero_headline_suffix' => ['nullable', 'string', 'max:255'],
            'home_section1_heading' => ['nullable', 'string', 'max:255'],
            'home_section1_body' => ['nullable', 'string', 'max:5000'],
            'home_stat_caption_guests' => ['nullable', 'string', 'max:80'],
            'home_stat_caption_rooms' => ['nullable', 'string', 'max:80'],
            'home_stat_caption_pools' => ['nullable', 'string', 'max:80'],
            'home_stat_caption_dining' => ['nullable', 'string', 'max:80'],
            'header_brand_lines' => ['nullable', 'string', 'max:2000'],
            'ui_page_heroes_json' => ['nullable', 'string', 'max:10000'],
        ];
    }
}
