<?php
return array (
  'language' =>
  array (
    'label' => 'Language',
    'menu_label' => 'Change language',
    'choose_language' => 'Choose your language',
    'close_menu' => 'Close language menu',
    'updated' => 'Your language preference has been updated (:language).',
  ),
  'alerts' => 
  array (
    'blocked_access' => 'Your access has been restricted.',
  ),
  'email' => 
  array (
    'common' => 
    array (
      'greeting' => 'Hello :name,',
      'greeting_generic' => 'Hello,',
      'signature' => 'See you soon,\\nThe :site team',
    ),
    'verification' => 
    array (
      'subject' => 'Confirm your email for :site',
      'intro' => 'Welcome to :site! To activate your account and start distributing your music, use the link below.',
      'action' => 'Verification link: :link',
      'footer' => 'If you did not request this registration, you can ignore this email.',
    ),
    'reset' => 
    array (
      'subject' => 'Reset your :site password',
      'intro' => 'You requested to reset your password on :site.',
      'action' => 'To choose a new password, open the following link: :link',
      'expiration' => 'This link will expire in 60 minutes. If you did not request it, you can safely ignore this message.',
    ),
  ),
  'auth' => 
  array (
    'roles' => 
    array (
      'musician' => 'Musician',
      'artist' => 'Artist',
      'manager' => 'Manager',
      'producer' => 'Producer',
      'publisher' => 'Publisher',
      'label' => 'Label',
      'other' => 'Other',
      'member' => 'Member',
    ),
    'common' => 
    array (
      'first_name_label' => 'First name',
      'last_name_label' => 'Last name',
      'email_label' => 'Email address',
      'country_label' => 'Country of residence',
      'role_label' => 'Your profile',
      'language_label' => 'Preferred language',
      'password_label' => 'Password',
      'confirm_password_label' => 'Confirm password',
    ),
    'login' => 
    array (
      'title' => 'Sign in',
      'lead' => 'Return to your dashboard and keep building your legend.',
      'submit' => 'Sign in',
      'forgot' => 'Forgot password?',
      'register_prompt' => 'No account yet? :link.',
      'register_link' => 'Sign up',
    ),
    'register' => 
    array (
      'intro_title' => 'Join :site and let AI amplify your voice.',
      'intro_text' => 'We connect every artist to the audience that is already waiting for their music. Sign up to launch your first release in minutes.',
      'bullets' => 
      array (
        'native_ai' => 'Native AI marketing to generate your campaigns, playlist pitches and fan insights.',
        'worldwide' => 'Instant worldwide distribution on more than 250 premium platforms.',
        'royalties' => 'Keep 100% of your royalties with transparent tracking and real-time alerts.',
      ),
      'title' => 'Create your account',
      'lead' => 'Share your information to receive your confirmation link.',
      'language_help' => 'We will personalise the dashboard, emails and onboarding in this language.',
      'submit' => 'Activate my account',
      'login_prompt' => 'Already a member? :link.',
      'login_link' => 'Sign in',
      'success' => 'Thank you! Check your inbox to confirm your address and activate your account.',
    ),
    'forgot' => 
    array (
      'title' => 'Forgot your password?',
      'lead' => 'Enter your email address to receive the reset instructions.',
      'submit' => 'Send reset link',
      'back_to_login' => 'Back to sign in',
      'success' => 'If an account matches this email address, we just sent you instructions to reset your password.',
    ),
    'reset' => 
    array (
      'title' => 'Choose a new password',
      'lead' => 'Pick a strong password to secure your account again.',
      'submit' => 'Update password',
      'token_invalid' => 'This reset link is invalid or incomplete. Request a new link.',
      'token_expired' => 'This reset link has expired. Request a new link.',
      'token_used' => 'This reset link is no longer valid. Request a new link.',
      'success' => 'Your password has been updated. You can now sign in.',
      'new_password_label' => 'New password',
      'confirm_password_label' => 'Confirm password',
      'request_new_link' => 'Request a new link',
      'return_to_login' => 'Return to sign in',
    ),
    'verify' => 
    array (
      'expired_title' => 'Expired or invalid link',
      'expired_body' => 'The verification link you used is no longer valid. Contact our support at :email to receive a new one.',
      'cta_login' => 'Back to sign in',
      'success' => 'Your email address has been confirmed. You can now sign in.',
    ),
    'blocked' => 
    array (
      'title' => 'Access restricted',
      'lead' => 'Need help? Write to :email and our team will assist you quickly.',
      'cta_login' => 'Back to sign in',
    ),
    'profile' => 
    array (
      'updated' => 'Changes saved.',
    ),
  ),
  'dashboard' => 
  array (
    'title' => 'Dashboard – :site',
    'brand_alt' => ':site dashboard',
    'profile_panel' => 
    array (
      'title' => 'Your profile',
      'helper' => 'Update your identity, country, language and profile picture to keep your presence consistent.',
      'remove_photo' => 'Remove photo',
      'remove_photo_sr' => 'Remove photo',
      'change_photo' => 'Change photo',
      'preview_alt' => 'Profile photo preview',
      'photo_alt' => 'Profile photo',
      'close' => 'Close panel',
      'labels' =>
      array (
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'country' => 'Country',
        'role' => 'Professional profile',
        'language' => 'Language',
        'currency' => 'Currency',
        'address_line1' => 'Address line 1',
        'address_line2' => 'Address line 2',
        'postal_code' => 'Postal code',
        'city' => 'City',
        'phone_number' => 'Phone number',
        'business_type' => 'Account type',
        'company_name' => 'Company name',
        'company_vat' => 'VAT / Tax ID',
      ),
      'language_help' => 'We will personalise the dashboard and notifications in this language.',
      'currency_help' => 'Prices and invoices will be displayed in :currency.',
      'business_type_helper' => 'Select whether you act as an individual or on behalf of a company.',
      'business_fields_helper' => 'Company details appear on invoices and admin exports.',
      'business_type_options' =>
      array (
        'individual' => 'Individual',
        'company' => 'Company',
      ),
      'submit' => 'Save changes',
      'submit_processing' => 'Saving…',
      'logout' => 'Log out',
      'feedback' =>
      array (
        'saving' => 'Saving…',
        'image_optimizing' => 'Optimising your image…',
        'image_ready' => 'Image ready to be saved.',
        'image_error' => 'An error occurred while processing your image.',
        'photo_removed' => 'Photo removed. Don’t forget to save.',
        'profile_refresh' => 'Your profile has been updated. Refreshing…',
        'profile_success' => 'Your profile has been updated successfully.',
        'profile_error' => 'Unable to update your profile.',
        'unexpected_error' => 'An unexpected error occurred. Please try again.',
      ),
    ),
    'welcome' =>
    array (
      'title' => 'Hi :name, ready to shake the world?',
      'body' => 'Your workspace centralises all the tools to turn your tracks into viral experiences. Prepare releases, orchestrate your distribution and follow every fan interaction in real time.',
    ),
    'studio_card' =>
    array (
      'aria_label' => 'Launch MusicDistro Studio',
      'badge' => 'NEW',
      'title' => 'Craft, mix and master in the browser',
      'subtitle' => 'Open the pro-grade Music Studio with timeline, mixer, plugins and real-time export.',
      'cta' => 'Open the Music Studio',
    ),
    'cards' =>
    array (
      'distribution' =>
      array (
        'title' => 'Music distribution',
        'description' => 'Launch new releases, create albums and monitor royalties from the connected distribution console. Your entire delivery chain is available in one click.',
        'link_label' => 'Open the distribution console',
        'type' => 'modal',
        'modal_target' => 'musicDistributionModal',
        'service_url' => '/generate-token/',
      ),
      'tutorial' =>
      array (
        'title' => 'Step-by-step distribution tutorial',
        'description' => 'Follow our visual guide to prepare masters, configure metadata and maximise impact on release day.',
        'link_label' => 'View the tutorial',
        'href' => '/tutorial.php',
      ),
      'royalties' =>
      array (
        'title' => 'Collect 100% of your royalties',
        'description' => 'Unlock premium payouts across Spotify, Apple Music, YouTube, Amazon Music and 150+ DSPs with a single upgrade.',
        'link_label' => 'Discover the premium plan',
        'type' => 'modal',
        'modal_target' => 'royaltiesModal',
        'variant' => 'highlight',
      ),
      'mastering' =>
      array (
        'title' => 'In-house AI mastering',
        'description' => 'Deliver radio-ready loudness and clarity in minutes. Drop your mixdown, explore pro presets and export instantly.',
        'link_label' => 'Launch the mastering studio',
        'type' => 'modal',
        'modal_target' => 'masteringModal',
        'variant' => 'highlight',
      ),
      'express_delivery' =>
      array (
        'title' => 'Express 24h delivery',
        'description' => 'Accelerate your distribution timeline with dedicated specialists delivering your release within 24 hours.',
        'link_label' => 'Book express delivery',
        'type' => 'modal',
        'modal_target' => 'expressDeliveryModal',
      ),
      'smartlinks' =>
      array (
        'title' => 'Smartlinks that convert',
        'description' => 'Generate landing pages that route fans to Spotify, Apple Music, Deezer, Amazon Music, YouTube Music and every connected DSP in seconds.',
        'link_label' => 'Manage smartlinks',
        'type' => 'modal',
        'modal_target' => 'smartlinksModal',
        'variant' => 'highlight',
      ),
      'ai_composer' =>
      array (
        'title' => 'AI music composer',
        'description' => 'Draft lyrics, audition Suno-inspired vocalists and render AI instrumentals from your creative brief.',
        'link_label' => 'Launch the AI composer',
        'type' => 'modal',
        'modal_target' => 'aiComposerModal',
        'variant' => 'highlight',
      ),
      'cloud_storage' =>
      array (
        'title' => 'Musicdistro Cloud for artists',
        'description' => 'Store, timestamp and share your masters in an encrypted vault backed by blockchain proof of ownership.',
        'link_label' => 'Discover Musicdistro Cloud',
        'type' => 'modal',
        'modal_target' => 'cloudStorageModal',
        'variant' => 'highlight',
        'service_url' => '/cloud-storage',
      ),
      'youtube' =>
      array (
        'title' => 'YouTube rights management',
        'description' => 'Synchronise YouTube Content ID and connect your official artist channels to centralise assets.',
        'link_label' => 'Configure YouTube',
        'type' => 'modal',
        'modal_target' => 'youtubeModal',
        'variant' => 'highlight',
      ),
      'publishing_administration' =>
      array (
        'title' => 'Publishing administration',
        'description' => 'Register compositions, collect neighbouring rights and monitor global royalty splits in real time.',
        'link_label' => 'Manage publishing',
        'type' => 'modal',
        'modal_target' => 'publishingAdministrationModal',
      ),
      'royalties_withdrawal' =>
      array (
        'title' => 'Royalty withdrawal',
        'description' => 'Initiate payouts, select bank accounts and track withdrawals across every catalogue in one place.',
        'link_label' => 'Withdraw royalties',
        'type' => 'modal',
        'modal_target' => 'royaltiesWithdrawalModal',
      ),
      'coaching' =>
      array (
        'title' => 'AI coaching & campaigns',
        'description' => 'Activate AI-powered campaigns, identify compatible playlists and receive a personalised promo plan.',
        'link_label' => 'Coming soon',
        'alert' => 'AI campaigns are coming soon.',
      ),
      'payments' =>
      array (
        'title' => 'Billing & payments',
        'description' => 'Review every Stripe payment, download invoices and keep an eye on upcoming renewals.',
        'link_label' => 'View payment history',
        'type' => 'modal',
        'modal_target' => 'paymentsModal',
      ),
    ),
    'sidebar' =>
    array (
      'badge' => 'Navigation',
      'title' => 'Workspace navigation',
      'subtitle' => 'Jump between dashboard areas and open the right tools instantly.',
      'links' =>
      array (
        'overview' =>
        array (
          'label' => 'Overview',
          'description' => 'Catch the latest highlights and onboarding tips.',
        ),
        'studio' =>
        array (
          'label' => 'Studio',
          'description' => 'Open the browser studio with timeline, mixer and exports.',
        ),
        'distribution' =>
        array (
          'label' => 'Distribution',
          'description' => 'Launch the release workspace without modal popups.',
        ),
        'royalties' =>
        array (
          'label' => 'Royalties',
          'description' => 'Preview upgrade plans and royalty automation.',
        ),
        'payments' =>
        array (
          'label' => 'Billing',
          'description' => 'Review invoices and manage payment history.',
        ),
        'profile' =>
        array (
          'label' => 'Profile',
          'description' => 'Open your identity and account preferences.',
        ),
      ),
    ),
    'sidebar_toggle' =>
    array (
      'collapse' => 'Collapse sidebar',
      'expand' => 'Expand sidebar',
    ),
    'musicdistribution_modal' =>
    array (
      'badge' => 'New',
      'title' => 'MusicDistribution console',
      'subtitle' => 'Coordinate every release with the dedicated MusicDistribution workspace. Launch projects, push deliveries and monitor royalties without leaving your dashboard.',
      'features_title' => 'What you can do inside',
      'features' =>
      array (
        0 => 'Create singles, EPs and albums with guided metadata flows.',
        1 => 'Push deliveries to 250+ DSPs and monitor ingestion status in real time.',
        2 => 'Consolidate statements, track advances and export royalty reports instantly.',
      ),
      'cta_label' => 'Launch MusicDistribution',
      'cta_processing' => 'Connecting to MusicDistribution…',
      'cta_error' => 'Pop-ups are blocked. Allow pop-ups and try again.',
      'opt_out_label' => 'Don’t show this introduction again',
      'disclaimer' => 'We will open MusicDistribution in a new tab using a secure single sign-on session.',
      'actions' =>
      array (
        'close' => 'Close',
      ),
      'cta_href' => '/generate-token/',
    ),
    'publishing_modal' =>
    array (
      'badge' => 'Publishing administration',
      'title' => 'Collect more royalties worldwide',
      'subtitle' => 'Songwriters earn more when we register every work, protect your copyrights and chase global collections for you.',
      'hero_highlight' => 'Trusted by independent writers and catalogues in 80+ countries.',
      'cta_primary' =>
      array (
        'label' => 'Get started now',
        'href' => '/publishing',
      ),
      'cta_secondary' =>
      array (
        'label' => 'Book a call with our team',
        'href' => 'mailto:publishing@musicdistro.io',
      ),
      'stats' =>
      array (
        0 =>
        array (
          'value' => '85%',
          'label' => 'Royalties paid to you',
        ),
        1 =>
        array (
          'value' => '200+',
          'label' => 'Societies & DSPs',
        ),
        2 =>
        array (
          'value' => '24h',
          'label' => 'Registration turnaround',
        ),
      ),
      'highlights_title' => 'What we do for songwriters',
      'highlights' =>
      array (
        0 =>
        array (
          'title' => 'Global work registration',
          'description' => 'Register your compositions across PROs, CMOs and the MLC so nothing is left unclaimed.',
        ),
        1 =>
        array (
          'title' => 'Neighbouring rights & sync',
          'description' => 'Claim neighbouring rights, TV & film royalties and pitch your songs for premium sync placements.',
        ),
        2 =>
        array (
          'title' => 'Analytics & split management',
          'description' => 'Track real-time statements, manage splits transparently and automate co-writer payouts.',
        ),
      ),
      'royalties' =>
      array (
        'title' => 'Collect more royalties',
        'subtitle' => 'Keep 100% of your copyrights and unlock new revenue streams without the admin.',
        'points' =>
        array (
          0 =>
          array (
            'title' => 'Streaming optimisation',
            'description' => 'Earn up to 20% more from Spotify and Apple Music thanks to worldwide collections and dispute management.',
          ),
          1 =>
          array (
            'title' => 'Media & live performance',
            'description' => 'Capture royalties from venues, radio, TV, festivals and social platforms in 70+ territories.',
          ),
          2 =>
          array (
            'title' => 'Creative services',
            'description' => 'Dedicated admin team, simplified cue sheets and sync-ready metadata for every song.',
          ),
        ),
      ),
      'pricing' =>
      array (
        'title' => 'Publishing pricing',
        'subtitle' => 'Simple onboarding, no hidden fees.',
        'cards' =>
        array (
          0 =>
          array (
            'value' => '$75',
            'label' => 'One-time setup fee',
            'description' => 'Unlimited work registrations and catalogues.',
          ),
          1 =>
          array (
            'value' => '15%',
            'label' => 'Collection commission',
            'description' => 'Keep 85% of the royalties we collect on your behalf.',
          ),
          2 =>
          array (
            'value' => '50%',
            'label' => 'Sync placements',
            'description' => 'Optional. Split only when we secure a sync deal for you.',
          ),
        ),
      ),
      'testimonial' =>
      array (
        'label' => 'Songwriter testimonial',
        'quote' => '“Musicdistro handles the paperwork so I can focus on writing. Collections have jumped without me chasing societies.”',
        'author' => 'Lena Martínez – Songwriter & producer',
      ),
      'footnote' => 'Ready to onboard your catalogue? Our publishing specialists migrate your works in under 10 days.',
      'actions' =>
      array (
        'close' => 'Close',
      ),
    ),
    'notifications' =>
    array (
      'panel' =>
      array (
        'title' => 'Notifications',
        'empty' => 'You are all caught up.',
        'toggle' => 'Open notifications',
        'close' => 'Close notifications panel',
      ),
      'actions' =>
      array (
        'open_profile' => 'Complete my profile',
        'open_link' => 'Open link',
      ),
      'items' =>
      array (
        'profile_incomplete' =>
        array (
          'title' => 'Complete your profile information',
          'message' => 'Add your address, country and phone number so we can finalise your registration.',
        ),
      ),
    ),
    'cloud_modal' =>
    array (
      'badge' => 'New',
      'title' => 'Musicdistro Cloud for artists',
      'subtitle' => 'Keep every mix, contract and stem in one secure workspace with blockchain-proofed priority.',
      'features_title' => 'Why artists love our cloud',
      'features' =>
      array (
        0 =>
        array (
          'title' => 'Blockchain ownership proof',
          'description' => 'Each upload is sealed with a timestamped hash so you can demonstrate authorship instantly.',
        ),
        1 =>
        array (
          'title' => 'Resilient creative vault',
          'description' => 'Store masters, stems, artwork and contracts with encrypted redundancy and smart organisation.',
        ),
        2 =>
        array (
          'title' => 'Collaborate with control',
          'description' => 'Share secure links with granular permissions, revoke access in one click and track activity.',
        ),
      ),
      'highlights_title' => 'What you get',
      'highlights' =>
      array (
        0 => 'Version tracking and detailed history for every asset.',
        1 => 'Real-time alerts whenever files are viewed or downloaded.',
        2 => 'Web and mobile access so your catalogue travels with you.',
      ),
      'pricing' =>
      array (
        'title' => 'Transparent pricing',
        'usage' =>
        array (
          'label' => 'Usage-based billing',
          'value' => ':price_mb per MB stored + :price_file per upload',
        ),
        'subscription' =>
        array (
          'label' => 'Subscription plan',
          'value' => ':price per month with :storage :unit included',
          'storage_unit' => 'MB',
        ),
      ),
      'cta_label' => 'Access the service',
      'cta_href' => '/cloud-storage',
      'opt_out_label' => 'Don’t show this introduction again',
      'disclaimer' => 'You can open Musicdistro Cloud anytime from your dashboard.',
      'actions' =>
      array (
        'close' => 'Close',
      ),
    ),
    'royalties_modal' =>
    array (
      'headline' => 'Upgrade to keep 100% of your royalties',
      'subheadline' => 'Stop sharing 30% of your revenue. Amplify every release with premium payouts, concierge support and laser-focused analytics.',
      'switch' =>
      array (
        'label' => 'Choose your billing frequency',
        'monthly' => 'Monthly',
        'yearly' => 'Yearly',
      ),
      'plans' =>
      array (
        'monthly' =>
        array (
          'label' => 'Monthly',
          'tagline' => 'Full flexibility – cancel whenever you need.',
          'currency' => '€',
          'price_main' => '9',
          'price_decimal' => '.99',
          'frequency' => '/month',
          'note' => 'Auto-renewing membership. Cancel anytime.',
          'cta' => 'Choose the monthly plan',
        ),
        'yearly' =>
        array (
          'label' => 'Yearly',
          'tagline' => 'Best value – get 2 months on us.',
          'currency' => '€',
          'price_main' => '99',
          'price_decimal' => '',
          'frequency' => '/year',
          'note' => 'Optimised for serious releases – save the equivalent of 2 months.',
          'cta' => 'Choose the annual plan',
        ),
      ),
      'features_title' => 'Everything you need to go pro',
      'features' =>
      array (
        0 => 'Keep 100% of your royalties on Spotify, Apple Music, YouTube, Amazon Music, Deezer and 150+ DSPs.',
        1 => 'Auto-generated invoices, VAT-ready statements and Stripe-powered security on every renewal.',
        2 => 'Priority distribution lane, release health monitoring and proactive revenue alerts.',
        3 => 'Real-time analytics on your streams and revenue performance.',
      ),
      'plan_highlights_title' => 'Practical details',
      'plan_highlights' =>
      array (
        0 => 'Instant activation as soon as the payment is confirmed.',
        1 => 'Cancel any time directly from your dashboard.',
        2 => 'Invoices are emailed to you automatically.',
      ),
      'guarantee' => 'Secure billing powered by Stripe. Transparent pricing, no hidden fees.',
      'actions' =>
      array (
        'close' => 'Close',
        'processing' => 'Connecting to Stripe…',
      ),
      'checkout' =>
      array (
        'generic_error' => 'Unable to start Stripe checkout. Please try again or contact support.',
        'missing_key' => 'Stripe configuration is missing. Contact your administrator.',
        'success_redirect' => 'Redirecting to secure payment…',
      ),
    ),
    'mastering_modal' =>
    array (
      'title' => 'Musicdistro Mastering Studio',
      'description' => 'Harness our in-house AI mastering engine to deliver louder, wider and cleaner masters without leaving your dashboard.',
      'dropzone' =>
      array (
        'title' => 'Drop your track',
        'subtitle' => 'WAV, AIFF or MP3 up to 250 MB.',
        'button' => 'Upload a file',
        'hint' => 'Or click to browse your computer.',
      ),
      'analysis' =>
      array (
        'title' => 'AI mastering console',
      ),
      'status' =>
      array (
        'idle' => 'Drag & drop your mix or click to browse.',
        'uploading' => 'Uploading your audio…',
        'analyzing' => 'Analysing transients and tone…',
        'rendering' => 'Rendering the final master…',
        'ready' => 'Master ready – explore the presets below.',
        'error' => 'Something went wrong. Please try again with another file.',
      ),
      'processing' =>
      array (
        'uploading' => 'Uploading to the mastering console…',
        'analyzing' => 'Mapping dynamics & stereo image…',
        'rendering' => 'Polishing your master…',
      ),
      'player' =>
      array (
        'title' => 'Preview & compare',
        'subtitle' => 'Select a preset and hit play to hear the upgrade.',
        'listen_master' => 'Listen to mastered version',
        'listen_original' => 'Toggle original mix',
        'duration_placeholder' => '—:—',
      ),
      'presets' =>
      array (
        'legend' => 'Each chain is engineered by our mastering team for different release scenarios.',
        'categories' =>
        array (
          'reference' => 'Reference',
          'impact' => 'Impact',
          'bounce' => 'Bounce',
          'spark' => 'Spark',
          'energy' => 'Energy',
          'groove' => 'Groove',
          'horizon' => 'Stage',
          'analog' => 'Analog',
          'air' => 'Air',
          'manual' => 'Custom',
        ),
        'original' => 'Original Mix',
        'radio' => 'Radio Hit',
        'hiphop' => 'Hip-Hop Drive',
        'electro' => 'Electro Spark',
        'edm' => 'EDM Max',
        'dance' => 'Dancefloor Glow',
        'festival' => 'Festival Impact',
        'warm' => 'Warm Tape',
        'spatial' => 'Spatial Air',
        'custom' => 'Custom Sculpt',
      ),
      'actions' =>
      array (
        'new_file' => 'Master another track',
      ),
      'visualizer' =>
      array (
        'title' => 'Real-time AI visualizer',
        'subtitle' => 'Monitor your master with live waveform and spectral energy analytics.',
        'waveform' => 'Neural oscilloscope',
        'spectrum' => 'Harmonic energy',
      ),
      'controls' =>
      array (
        'title' => 'Advanced controls',
        'subtitle' => 'Shape your master like a modern VST engineer: tweak dynamics, tone and stereo image in real time.',
        'pre_gain' => 'Input gain',
        'threshold' => 'Compressor threshold',
        'ratio' => 'Compression ratio',
        'attack' => 'Attack',
        'release' => 'Release',
        'low' => 'Low enhancer',
        'mid' => 'Mid sculpt',
        'high' => 'Air boost',
        'width' => 'Stereo width',
        'output' => 'Output loudness',
        'mono' => 'Mono monitor',
      ),
      'errors' =>
      array (
        'invalid_type' => 'Please upload a WAV, AIFF or MP3 file.',
        'too_large' => 'The file is too large. The limit is 250 MB.',
        'load' => 'Unable to read the audio file. Try exporting it again.',
      ),
      'checkout' =>
      array (
        'title' => 'Export your master',
        'hint' => 'Secure Stripe checkout – instant receipt and invoice.',
        'single' =>
        array (
          'label' => 'Master this track • :price',
          'description' => 'Download WAV + MP3 masters and stems for this title.',
          'product_name' => 'Musicdistro Mastering – single track',
          'product_description' => 'One-off AI mastering with 7-day revision window.',
        ),
        'subscription' =>
        array (
          'label' => 'Unlimited mastering • :price_month/mo billed yearly (:price_year/yr)',
          'description' => 'Unlimited exports, priority processing and reference matching.',
          'product_name' => 'Musicdistro Mastering – unlimited annual pass',
          'product_description' => '12 months of unlimited mastering across every release.',
        ),
        'processing_label' => 'Connecting to Stripe…',
        'success_redirect' => 'Redirecting to Stripe Checkout…',
        'generic_error' => 'Unable to start Stripe checkout. Please try again.',
        'missing_key' => 'Stripe is not configured yet. Please contact support.',
        'success_single' => 'Payment confirmed! Your master will be delivered shortly.',
        'success_yearly' => 'Welcome to unlimited mastering – your annual pass is active.',
        'cancel_single' => 'Checkout cancelled. No charges were made.',
        'cancel_yearly' => 'Subscription checkout cancelled. You were not charged.',
        'disabled' => 'Mastering payments are disabled by the administrator.',
      ),
      'download' =>
      array (
        'label' => 'Download mastered file',
        'description' => 'Export the mastered WAV directly from your browser — no payment required.',
        'hint' => 'Instant export of your master in high-resolution WAV format.',
        'processing' => 'Rendering mastered WAV…',
        'success' => 'Master export ready — your download should start shortly.',
        'error' => 'Unable to export the mastered file. Please try again.',
        'unsupported' => 'Master export is not supported in this browser. Try Chrome or Edge.',
        'unavailable' => 'Drop a track and let the AI finish mastering before exporting.',
      ),
    ),
    'smartlinks_modal' =>
    array (
      'badge' => 'Beta',
      'title' => 'Musicdistro Smartlinks',
      'description' => 'Create multi-platform landing pages in seconds and monitor clicks, conversions and geography – a Linkfire alternative built into your dashboard.',
      'tabs' =>
      array (
        'create' => 'Create smartlink',
        'analytics' => 'Analytics & history',
      ),
      'form' =>
      array (
        'upc_label' => 'UPC or release ID',
        'upc_placeholder' => 'e.g. 123456789012',
        'slug_label' => 'Custom URL (optional)',
        'slug_placeholder' => 'my-album-launch',
        'slug_hint' => 'Leave blank to auto-generate a clean URL.',
        'platforms_label' => 'Connected DSPs',
        'platforms_hint' => 'Toggle the services to display on your landing page.',
        'submit' => 'Generate smartlink',
        'processing' => 'Generating smartlink…',
      ),
      'preview' =>
      array (
        'title' => 'Landing preview',
        'subtitle' => 'Share the link below to let fans choose their favourite platform.',
        'share_label' => 'Shareable link',
        'copy' => 'Copy link',
        'copied' => 'Copied!',
        'empty' => 'Your smartlink preview will appear here once generated.',
        'cta_label' => 'Listen on',
      ),
      'success' =>
      array (
        'title' => 'Smartlink ready to share',
        'message' => 'Your landing page is live. Copy the link and monitor performance from the Analytics tab.',
      ),
      'history' =>
      array (
      'title' => 'Latest smartlinks',
      'empty' => 'No smartlinks yet. Generate your first one above.',
      'created' => 'Created :date',
      'analytics' => 'View analytics',
      'delete' => 'Delete',
      'confirm_delete' => 'Delete the smartlink ":name"? Stored analytics will be removed.',
      'deleted' => 'Smartlink deleted.',
    ),
      'analytics' =>
      array (
        'title' => 'Performance analytics',
        'empty' => 'Create a smartlink to unlock analytics.',
        'selector_label' => 'Your smartlinks',
        'summary' =>
        array (
          'clicks' => 'Total clicks',
          'ctr' => 'Click-through rate',
          'conversions' => 'Streaming conversions',
          'saves' => 'Saves & pre-adds',
        ),
        'geo_title' => 'Global reach',
        'geo_subtitle' => 'Each pulse represents a country engaging with your smartlink.',
        'platforms_title' => 'Platform breakdown',
        'cities_title' => 'Top cities',
        'timeline_title' => 'Engagement timeline',
        'recent_title' => 'Recent highlights',
      ),
      'actions' =>
      array (
        'view_analytics' => 'View analytics',
      ),
      'errors' =>
      array (
        'upc_required' => 'Enter your UPC to generate a smartlink.',
        'spotify_failback' => 'We couldn’t find a Spotify link for this UPC yet. Double-check the code or wait a little longer before generating your smartlink.',
      ),
    ),
    'express_modal' =>
    array (
      'badge' => '24H',
      'title' => 'Express 24h delivery',
      'subtitle' => 'Guarantee your next release reaches stores within 24 hours thanks to priority ingestion and dedicated specialists.',
      'actions' =>
      array (
        'close' => 'Close',
      ),
      'highlights' =>
      array (
        0 =>
        array (
          'title' => 'Priority ingestion lane',
          'description' => 'Fast-track delivery across DSPs with manual validation and queue bypassing.',
        ),
        1 =>
        array (
          'title' => 'Metadata concierge',
          'description' => 'A specialist reviews your audio, artwork and metadata before dispatch.',
        ),
        2 =>
        array (
          'title' => 'Launch day updates',
          'description' => 'Stay informed with direct updates until every store confirms your release.',
        ),
      ),
      'form' =>
      array (
        'title' => 'Reserve your express slot',
        'subtitle' => 'We pre-fill your contact details so you can confirm the essentials in seconds.',
        'fields' =>
        array (
          'first_name' => 'First name',
          'last_name' => 'Last name',
          'email' => 'Email',
          'release_title' => 'Release title',
          'release_title_placeholder' => 'Enter the single, EP or album title',
          'release_type' => 'Select the format',
        ),
        'types' =>
        array (
          'single' =>
          array (
            'label' => 'Single',
            'description' => 'Up to 1 track. Perfect for a headline single drop.',
          ),
          'ep' =>
          array (
            'label' => 'EP',
            'description' => '3 to 5 tracks packaged as a focused EP.',
          ),
          'album' =>
          array (
            'label' => 'Album',
            'description' => '6+ tracks delivered as a full-length release.',
          ),
          'unavailable' => 'Set a price in the admin panel to enable this format.',
        ),
        'actions' =>
        array (
          'submit_default' => 'Select a format',
          'submit_label' => 'Pay :price',
          'processing' => 'Redirecting…',
        ),
        'feedback' =>
        array (
          'missing_fields' => 'Fill in your release title and choose a format to continue.',
          'missing_format' => 'Choose which format you want to deliver with express service.',
          'error' => 'Unable to start the express checkout. Please try again.',
          'success' => 'Redirecting to Stripe…',
        ),
      ),
      'checkout' =>
      array (
        'single' =>
        array (
          'product_name' => 'Express delivery – Single',
          'product_description' => 'Priority 24-hour distribution for a single track.',
        ),
        'ep' =>
        array (
          'product_name' => 'Express delivery – EP',
          'product_description' => 'Priority delivery for 3 to 5 track EPs with manual validation.',
        ),
        'album' =>
        array (
          'product_name' => 'Express delivery – Album',
          'product_description' => 'Priority distribution for full-length albums with concierge support.',
        ),
      ),
    ),
    'payments' =>
    array (
      'badge' => 'Stripe billing',
      'title' => 'Billing & payments',
      'subtitle' => 'Track every transaction, download invoices and manage renewals without leaving the dashboard.',
      'refresh' => 'Refresh',
      'loading' => 'Loading payments…',
      'retry' => 'Try again',
      'updated_at' => 'Updated :time',
      'errors' =>
      array (
        'generic' => 'Unable to load your payments. Please try again.',
        'missing_key' => 'Stripe is not configured yet. Contact your administrator.',
        'unreachable' => 'We could not reach Stripe. Please try again shortly.',
      ),
      'upcoming' =>
      array (
        'title' => 'Upcoming renewals',
        'subtitle' => 'Stay informed about scheduled charges and renewal dates.',
        'empty' => 'No renewals scheduled.',
        'scheduled_on' => 'Scheduled for :date',
        'cancelled_on' => 'Ends on :date',
        'intervals' =>
        array (
          'week' => 'week',
          'month' => 'month',
          'year' => 'year',
        ),
      ),
      'history' =>
      array (
        'title' => 'Payment history',
        'subtitle' => 'All Stripe charges, invoices and receipts in one place.',
        'empty' => 'No payments recorded yet.',
        'subscription_tag' => 'Subscription',
        'one_time_tag' => 'One-time',
        'interval_label' => 'Every :interval',
      ),
      'table' =>
      array (
        'date' => 'Date',
        'service' => 'Service',
        'amount' => 'Amount',
        'status' => 'Status',
        'actions' => 'Actions',
      ),
      'actions' =>
      array (
        'close' => 'Close payment history',
        'retry' => 'Retry payment',
        'view_invoice' => 'View invoice',
        'download_invoice' => 'Download invoice',
        'view_receipt' => 'View receipt',
      ),
      'status' =>
      array (
        'paid' => 'Paid',
        'succeeded' => 'Paid',
        'processing' => 'Processing',
        'open' => 'Pending',
        'draft' => 'Draft',
        'uncollectible' => 'Failed',
        'void' => 'Cancelled',
        'past_due' => 'Past due',
        'requires_payment_method' => 'Update payment method',
        'requires_action' => 'Action required',
        'incomplete' => 'Incomplete',
        'incomplete_expired' => 'Expired',
        'canceled' => 'Cancelled',
        'active' => 'Active',
        'trialing' => 'Trialing',
        'scheduled' => 'Scheduled',
        'failed' => 'Failed',
        'unpaid' => 'Unpaid',
        'unknown' => 'Unknown',
      ),
      'category_labels' =>
      array (
        'royalties' => 'Royalties',
        'mastering' => 'Mastering',
        'smartlinks' => 'Smartlinks',
        'other' => 'Other services',
      ),
      'plan_labels' =>
      array (
        'monthly' => 'Premium royalties – monthly',
        'yearly' => 'Premium royalties – yearly',
        'mastering_single' => 'AI mastering – single export',
        'mastering_yearly' => 'AI mastering – annual pass',
      ),
    ),
    'admin' =>
    array (
      'title' => 'Administration',
      'subtitle' => 'Review every account. These insights are only visible to super administrators.',
      'tabs' =>
      array (
        'users' => 'Users',
        'payments' => 'Monetization',
        'notifications' => 'Notifications',
        'newsletter' => 'Newsletter',
        'ai' => 'AI tools',
        'design' => 'Design',
        'configuration' => 'Configuration',
        'distribution' => 'Distribution',
      ),
      'stripe' =>
      array (
        'title' => 'Stripe configuration',
        'description' => 'Plug your Stripe API keys to activate premium subscriptions and automatic renewals.',
        'secret_label' => 'Stripe secret key',
        'publishable_label' => 'Stripe publishable key (optional)',
        'helper' => 'We only store the keys encrypted in your private database. Update them anytime.',
        'submit' => 'Save Stripe settings',
        'feedback' =>
        array (
          'processing' => 'Saving Stripe settings…',
          'saved' => 'Stripe settings updated successfully.',
          'error' => 'Unable to save Stripe settings. Please check your keys and try again.',
        ),
      ),
      'monetization' =>
      array (
        'title' => 'Mastering monetization',
        'description' => 'Control how artists access the mastering studio and whether Stripe checkout is required.',
        'currency_section_title' => 'Billing currencies',
        'currency_section_description' => 'Configure the currencies available across all paid services.',
        'payments_label' => 'Charge for mastering exports',
        'payments_enabled' => 'Stripe checkout cards will be displayed before users can download masters.',
        'payments_disabled' => 'Users can export their masters instantly with no payment required.',
        'single_label' => 'Single master price',
        'single_hint' => 'Displayed on the one-off mastering card (default currency).',
        'yearly_label' => 'Yearly mastering pass price',
        'yearly_hint' => 'Total amount billed once per year (default currency).',
        'currency_default_label' => 'Default billing currency',
        'currency_default_helper' => 'All prices are stored in this currency and converted for users.',
        'currency_allow_label' => 'Allow artists to choose their currency',
        'currency_enabled_label' => 'Currencies available to artists',
        'currency_enabled_helper' => 'Select the currencies you want to offer alongside the default one.',
        'publishing_title' => 'Publishing administration',
        'publishing_description' => 'Set the one-off onboarding fee for publishing administration.',
        'publishing_price_label' => 'Publishing setup price',
        'publishing_price_helper' => 'Displayed on the publishing administration card (default currency).',
        'express_title' => 'Express 24h delivery',
        'express_description' => 'Define the express shipping price for singles, EPs and albums.',
        'express_single_label' => 'Express price – Single',
        'express_single_helper' => 'Shown in the express delivery modal when a single (1 track) is selected.',
        'express_ep_label' => 'Express price – EP',
        'express_ep_helper' => 'Displayed for express EP deliveries (3 to 5 tracks).',
        'express_album_label' => 'Express price – Album',
        'express_album_helper' => 'Displayed for full album express deliveries (6+ tracks).',
        'invalid_price' => 'Enter a valid mastering price (e.g. 9.99).',
        'invalid_publishing_price' => 'Enter a valid publishing setup price (e.g. 75).',
        'invalid_express_price' => 'Enter valid express delivery prices for each format (e.g. 39.99).',
      ),
      'cloud_storage' =>
      array (
        'title' => 'Musicdistro Cloud',
        'description' => 'Set pricing for the secure artist cloud service.',
        'usage' =>
        array (
          'title' => 'Usage-based billing',
          'description' => 'Charge per megabyte stored and per uploaded file.',
          'toggle_label' => 'Enable usage-based billing',
          'price_mb_label' => 'Price per MB',
          'price_mb_helper' => 'Billed on the average storage footprint per month.',
          'price_file_label' => 'Price per file',
          'price_file_helper' => 'Charged once per upload regardless of file size.',
          'state_enabled' => 'Usage-based billing enabled',
          'state_disabled' => 'Usage-based billing disabled',
        ),
        'subscription' =>
        array (
          'title' => 'Subscription offer',
          'description' => 'Offer a monthly bundle with included storage.',
          'toggle_label' => 'Enable subscription offer',
          'price_label' => 'Subscription price',
          'price_helper' => 'Recurring amount charged to the artist.',
          'storage_label' => 'Included storage quota',
          'storage_helper' => 'Define how many megabytes are included in the plan.',
          'storage_suffix' => 'MB',
          'state_enabled' => 'Subscription enabled',
          'state_disabled' => 'Subscription disabled',
        ),
        'validation_usage' => 'Provide a price per MB and per file to enable usage-based billing.',
        'validation_subscription' => 'Set a subscription price and storage quota to enable the offer.',
      ),
      'notifications' =>
      array (
        'title' => 'Dashboard notifications',
        'description' => 'Control the notifications that appear in the artist dashboard.',
        'display_label' => 'Display notifications icon',
        'display_helper' => 'Hide the bell icon from the dashboard when disabled.',
        'automations_title' => 'Automated alerts',
        'automations_description' => 'Select the automated reminders that will be sent to users.',
        'profile_incomplete_label' => 'Remind users to complete their profile information',
        'profile_incomplete_helper' => 'Sends a reminder when the address, country or phone number is missing.',
        'broadcast' =>
        array (
          'title' => 'Custom broadcast',
          'description' => 'Send a personalised notification to every artist dashboard.',
          'helper' => 'Translate the message for each language. Empty fields inherit the English version.',
          'link_label' => 'Notification link (optional)',
          'link_placeholder' => 'https://musicdistro.io/updates',
          'link_helper' => 'Add a destination URL for the call to action.',
          'translations_label' => 'Translations',
          'translations_helper' => 'Expand a language to customise the title, message and button copy.',
          'fields' =>
          array (
            'title' => 'Notification title',
            'message' => 'Message',
            'action_label' => 'Call to action label',
          ),
          'submit' => 'Send notification',
          'feedback' =>
          array (
            'processing' => 'Sending notification…',
            'success' => 'Notification delivered to every dashboard.',
            'error' => 'Unable to send the notification.',
            'missing' => 'Provide at least a title and message for one language.',
            'invalid_link' => 'Enter a valid URL (starting with http:// or https://) or leave the link empty.',
          ),
        ),
        'submit' => 'Save notification settings',
        'feedback' =>
        array (
          'saved' => 'Notification settings have been updated.',
          'error' => 'Unable to save notification settings.',
          'processing' => 'Saving…',
        ),
      ),
      'ai' =>
      array (
        'title' => 'AI composer settings',
        'description' => 'Store the Suno credentials used when artists generate music with the AI composer.',
        'status' =>
        array (
          'configured' => 'A Suno API key is currently configured.',
          'missing' => 'No Suno API key is configured yet. Artists will see an error until you add one.',
        ),
        'fields' =>
        array (
          'api_key_label' => 'Suno API key',
          'api_key_placeholder' => 'sk_...',
          'api_key_helper' => 'We keep the key encrypted and only use it for AI music generation requests.',
        ),
        'submit' => 'Save AI settings',
        'feedback' =>
        array (
          'processing' => 'Saving AI settings…',
          'saved' => 'AI settings updated successfully.',
          'error' => 'Unable to save AI settings. Please try again.',
        ),
      ),
      'design' =>
      array (
        'title' => 'Dashboard design',
        'description' => 'Choose the layout artists experience when they land on the dashboard.',
        'helper' => 'Changing the layout updates the interface for every artist after their next page load.',
        'preview_badge' => 'Preview',
        'actions' =>
        array (
          'submit' => 'Save design',
          'processing' => 'Updating design…',
        ),
        'feedback' =>
        array (
          'saved' => 'Dashboard design updated.',
          'error' => 'Unable to save the dashboard design. Please try again.',
        ),
        'notice' =>
        array (
          'eyebrow' => 'Design change',
          'title' => 'Previewing {design}',
          'message' => 'You switched to the {design} dashboard design. Keep this layout or revert to the previous one?',
          'confirm' => 'Keep design',
          'cancel' => 'Revert',
        ),
        'validation' =>
        array (
          'variant' => 'Select a dashboard design before saving.',
        ),
        'branding' =>
        array (
          'title' => 'Brand identity',
          'description' => 'Update the visual identity that appears across the dashboard and public metadata.',
          'fields' =>
          array (
            'site_name' =>
            array (
              'label' => 'Site name',
              'helper' => 'Used in metadata, dashboards and transactional emails.',
            ),
            'dashboard_logo' =>
            array (
              'label' => 'Dashboard logo',
              'helper' => 'Upload a horizontal logo. Recommended size: 240 × 64 px.',
              'preview_alt' => ':site dashboard logo preview',
            ),
            'favicon' =>
            array (
              'label' => 'Favicon',
              'helper' => 'PNG, SVG or ICO – displayed in browser tabs and share previews.',
              'preview_alt' => ':site favicon preview',
            ),
            'footer_copyright' =>
            array (
              'label' => 'Footer copyright line',
              'helper' => 'Displayed at the bottom of the dashboard. Supports :year and :site placeholders.',
              'placeholder' => '© :year :site. Amplify your emotions, not your paperwork.',
            ),
          ),
          'actions' =>
          array (
            'submit' => 'Save branding',
            'processing' => 'Updating branding…',
          ),
          'feedback' =>
          array (
            'saved' => 'Branding updated successfully.',
            'error' => 'Unable to save the branding. Please try again.',
          ),
          'validation' =>
          array (
            'site_name' => 'Enter a site name to continue.',
            'site_name_max' => 'Site name must be 120 characters or fewer.',
            'dashboard_logo' => 'Upload a PNG, JPG, WebP or SVG logo under 4 MB.',
            'favicon' => 'Upload a PNG, SVG or ICO favicon under 1 MB.',
            'footer_copyright_max' => 'Footer line must be 160 characters or fewer.',
          ),
        ),
        'variants' =>
        array (
          'classic' =>
          array (
            'title' => 'Classic',
            'description' => 'Original MusicDistro layout with neon depth and stacked modules.',
            'highlights' =>
            array (
              0 => 'Dense shortcut grid tailored for power users.',
              1 => 'Vivid gradients and layered glassmorphism.',
            ),
          ),
          'vision' =>
          array (
            'title' => 'Vision',
            'description' => 'Refined two-column layout inspired by Apple and OpenAI dashboards.',
            'highlights' =>
            array (
              0 => 'Glass panels with softer lighting and generous spacing.',
              1 => 'Adaptive 12-column grid to organise quick actions cleanly.',
              2 => 'Hero welcome card with modern typography and gradients.',
            ),
          ),
          'focus' =>
          array (
            'title' => 'Focus',
            'description' => 'Streamlined layout built for clarity with calm surfaces and balanced spacing.',
            'highlights' =>
            array (
              0 => 'Centred navigation header with softened glass treatment.',
              1 => 'Three-column action grid that groups tasks by priority.',
              2 => 'Simplified cards and buttons for faster scanning on any device.',
            ),
          ),
          'aura' =>
          array (
            'title' => 'Aura',
            'description' => 'Immersive layout with a persistent sidebar, full-screen workspaces and fluid navigation.',
            'highlights' =>
            array (
              0 => 'Left-hand command bar with contextual shortcuts and actions.',
              1 => 'Cards breathe with larger spacing and luminous gradients.',
              2 => 'Modal workflows expand into full-view canvases for focus.',
            ),
          ),
        ),
      ),
      'newsletter' =>
      array (
        'title' => 'Newsletter campaigns',
        'description' => 'Compose and send email campaigns to your users.',
        'sender' =>
        array (
          'title' => 'Sender',
          'name_label' => 'Sender name',
          'email_label' => 'Sender email',
          'reply_to_label' => 'Reply-to address (optional)',
          'reply_to_placeholder' => 'support@musicdistro.io',
          'helper' => 'Emails are delivered individually with this identity.',
        ),
        'recipients' =>
        array (
          'title' => 'Recipients',
          'helper' => 'Choose who should receive the campaign.',
          'mode_all' => 'All users',
          'mode_selected' => 'Select recipients',
          'select_label' => 'Select users',
          'select_helper' => 'Hold Ctrl (Windows) or Command (macOS) to select multiple users.',
          'empty' => 'No recipients available.',
          'additional_label' => 'Additional email addresses',
          'additional_placeholder' => 'artist@example.com, manager@example.com',
        ),
        'content' =>
        array (
          'title' => 'Content',
          'subject_label' => 'Email subject',
          'subject_placeholder' => 'Announce your latest updates',
          'html_label' => 'HTML content',
          'html_placeholder' => '<h1>Ready for your next release?</h1>',
          'helper' => 'Use HTML to design a rich email. Plain text is used as a fallback.',
          'text_label' => 'Plain text version',
          'text_placeholder' => 'Hello! Here is what is new…',
        ),
        'delivery' =>
        array (
          'title' => 'Delivery',
          'transport_label' => 'Send using',
          'transport_options' =>
          array (
            'phpmail' => 'PHP mail()',
            'smtp' => 'Custom SMTP server',
          ),
          'batch_label' => 'Messages per batch',
          'interval_label' => 'Interval between batches',
          'interval_helper' => 'Avoid provider limits by spacing out deliveries.',
          'interval_unit' =>
          array (
            'seconds' => 'seconds',
            'minutes' => 'minutes',
            'hours' => 'hours',
          ),
          'smtp' =>
          array (
            'host_label' => 'SMTP host',
            'port_label' => 'Port',
            'encryption_label' => 'Encryption',
            'encryption_none' => 'None',
            'encryption_ssl' => 'SSL/TLS',
            'encryption_tls' => 'STARTTLS',
            'username_label' => 'Username',
            'password_label' => 'Password',
          ),
        ),
        'submit' => 'Send newsletter',
        'feedback' =>
        array (
          'processing' => 'Sending newsletter…',
          'success' => 'Newsletter sent to :count recipients.',
          'partial' => 'Newsletter sent to :sent recipients. :failed deliveries failed.',
          'error' => 'Unable to send the newsletter.',
        ),
        'validation' =>
        array (
          'subject' => 'Enter a subject for the newsletter.',
          'html' => 'Provide HTML content for the newsletter.',
          'recipients' => 'Select at least one recipient or provide email addresses.',
          'sender_email' => 'Enter a valid sender email address.',
          'reply_to' => 'Enter a valid reply-to email address.',
          'smtp_host' => 'Provide the SMTP host.',
          'smtp_port' => 'Enter a valid SMTP port.',
        ),
      ),
      'translations' =>
      array (
        'title' => 'Manage translations',
        'description' => 'Browse every translation key and update the content for each language.',
        'search_label' => 'Search translation keys',
        'search_placeholder' => 'Search keys or text',
        'loading' => 'Loading translations…',
        'error' => 'Unable to load translations. Please try again.',
        'retry' => 'Retry',
        'empty' => 'No translations match your search.',
        'close' => 'Close',
        'save' => 'Save translations',
        'feedback' =>
        array (
          'saving' => 'Saving translations…',
          'saved' => 'Translations saved successfully.',
          'error' => 'Unable to save translations. Please try again.',
        ),
        'table' =>
        array (
          'label' => 'Translations table',
          'key' => 'Key',
        ),
        'pagination' =>
        array (
          'previous' => 'Previous',
          'next' => 'Next',
          'status' => 'Page {page} of {pages} · Showing {start}–{end} of {total} keys',
        ),
      ),
      'configuration' =>
      array (
        'title' => 'Dashboard configuration',
        'description' => 'Enable or hide the dashboard modules available to artists.',
        'studio' =>
        array (
          'title' => 'Studio card',
          'description' => 'Control whether the Music Studio promotion card is visible.',
          'toggle_label' => 'Display the Music Studio card',
        ),
        'languages' =>
        array (
          'title' => 'Languages',
          'description' => 'Allow artists to switch languages and choose which locales are available.',
          'toggle_label' => 'Enable multilingual interface',
          'default_label' => 'Default language',
          'default_helper' => 'Used when artists first sign in or when automatic detection is disabled.',
          'auto_detect_label' => 'Detect the artist’s language automatically',
          'auto_detect_helper' => 'Enables matching the interface to the browser language when available.',
          'empty' => 'No languages are currently available.',
          'manage_button' => 'Manage translations',
          'minimum_error' => 'Select at least one language when multilingual mode is enabled.',
        ),
        'cards' =>
        array (
          'title' => 'Shortcut cards',
          'description' => 'Choose which shortcut cards appear on the dashboard.',
          'toggle_label' => 'Display ":card" card',
          'empty' => 'No cards are available at the moment.',
        ),
        'submit' => 'Save configuration',
        'feedback' =>
        array (
          'processing' => 'Saving configuration…',
          'saved' => 'Dashboard configuration updated.',
          'error' => 'Unable to save the configuration. Please try again.',
        ),
      ),
      'distribution' =>
      array (
        'title' => 'Distribution configuration',
        'description' => 'Select the dashboard powering your distribution workflows.',
        'provider_label' => 'Distribution dashboard',
        'providers' =>
        array (
          'sonosuite' => 'Sonosuite in-house dashboard',
        ),
        'fields' =>
        array (
          'sonosuite' =>
          array (
            'base_url_label' => 'Sonosuite platform URL',
            'base_url_placeholder' => 'https://platform.musicdistribution.cloud',
            'shared_secret_label' => 'SSO shared secret',
            'helper' => 'Used to initiate SSO sessions with the Sonosuite platform.',
          ),
        ),
        'submit' => 'Save distribution settings',
        'feedback' =>
        array (
          'processing' => 'Saving distribution settings…',
          'saved' => 'Distribution settings updated successfully.',
          'error' => 'Unable to save distribution settings. Please try again.',
        ),
      ),
      'table' =>
      array (
        'headers' =>
        array (
          'id' => 'ID',
          'name' => 'Full name',
          'email' => 'Email',
          'country' => 'Country',
          'created_at' => 'Created on',
          'last_login_at' => 'Last login',
          'last_login_ip' => 'Last login IP',
          'status' => 'Status',
          'actions' => 'Actions',
        ),
        'empty' => 'No account registered yet.',
        'status' => 
        array (
          'verified' => 'Verified',
          'pending' => 'Pending',
          'blocked' => 'Blocked',
          'super_admin' => 'Super admin',
        ),
        'actions' =>
        array (
          'block' => 'Block access',
          'unblock' => 'Restore access',
          'impersonate' => 'Log in as user',
          'delete' => 'Delete',
          'self' => 'Action unavailable on your account',
        ),
        'confirm' =>
        array (
          'delete' => 'Confirm deleting this account?',
          'block' => 'Block this user’s access?',
          'unblock' => 'Restore this user’s access?',
          'impersonate' => 'Log in as this user?',
        ),
        'feedback' => 
        array (
          'processing' => 'Processing…',
          'success' => 'Action completed successfully.',
          'error' => 'Action could not be completed. Please try again.',
        ),
      ),
      'login_history' =>
      array (
        'badge' => 'Security',
        'title' => 'Login activity',
        'subtitle' => 'Recent sign-ins for :name',
        'subtitle_generic' => 'Recent sign-ins',
        'button_label' => 'Log',
        'open_label' => 'Show login history',
        'close' => 'Close login history',
        'back' => 'Back',
        'loading' => 'Retrieving login activity…',
        'error' => 'Unable to load login activity.',
        'empty' => 'No sign-in activity recorded yet.',
        'footer' => 'IPs and devices are stored to protect your account.',
        'current_badge' => 'Most recent',
        'device_summary' => ':device • :os • :browser',
        'device_summary_fallback' => 'Device information unavailable',
        'device_unknown' => 'Device',
        'os_unknown' => 'OS',
        'browser_unknown' => 'Browser',
        'user_agent_label' => 'User agent',
        'time_label' => 'Signed in on :value',
        'unknown_ip' => 'Unknown IP',
      ),
      'user_modal' =>
      array (
        'badge' => 'Profile',
        'title' => 'User details',
        'subtitle' => 'Account created on :date',
        'close' => 'Close user details',
        'helper' => 'Information collected during registration and profile updates.',
        'open' => 'View details',
        'open_aria' => 'View details for :name',
        'empty' => '—',
        'labels' =>
        array (
          'profile' => 'Professional profile',
          'language' => 'Language',
          'status' => 'Status',
          'country' => 'Country',
          'phone' => 'Phone number',
          'address' => 'Address',
          'created_at' => 'Created on',
          'created_ip' => 'Created from IP',
          'last_login_at' => 'Last login',
          'last_login_ip' => 'Last login IP',
          'company_type' => 'Account type',
          'company_name' => 'Company name',
          'company_vat' => 'VAT / Tax ID',
        ),
        'company_types' =>
        array (
          'individual' => 'Individual',
          'company' => 'Company',
        ),
      ),
      'impersonation' =>
      array (
        'active' => 'You are viewing the dashboard as :name.',
        'return' => 'Return to admin view',
        'processing' => 'Restoring your administrator session…',
        'processing_button' => 'Restoring…',
        'error' => 'Unable to restore your administrator session. Please try again.',
        'started' => 'Switched to the selected account. Redirecting…',
        'stopped' => 'Administrator view restored.',
        'already_active' => 'You are already impersonating another user. Stop the current session before starting a new one.',
        'not_active' => 'No active impersonation session.',
        'blocked' => 'You cannot impersonate a blocked user.',
        'default_admin' => 'Administrator',
      ),
    ),
    'checkout' =>
    array (
      'success' => 'Payment confirmed. Welcome to full royalty collection!',
      'success_plan' => 'Payment confirmed. :plan plan activated — welcome to full royalty collection!',
      'cancel' => 'Checkout cancelled. Your premium upgrade has not been activated.',
    ),
    'footer' => '© :year :site. Amplify your emotions, not your paperwork.',
    'js' => 
    array (
      'unexpected_admin' => 'Unexpected response from the admin endpoint.',
      'unexpected_profile' => 'Unexpected response when updating the profile.',
      'admin_error' => 'Action could not be completed. Please try again.',
      'admin_success' => 'Action completed successfully.',
      'read_file_error' => 'Cannot read the file.',
      'load_image_error' => 'Cannot load the image.',
      'process_image_error' => 'Unable to process your image in this browser.',
      'compress_image_error' => 'Image compression failed.',
    ),
  ),
  'home' => 
  array (
    'title' => 'MusicDistro.io – Distribute your music with AI',
    'meta' => 
    array (
      'description' => 'MusicDistro.io is the AI-powered digital distribution platform that helps artists, labels and managers turn every release into a global success. Release your tracks, sync your marketing campaigns and keep 100% of your royalties.',
      'keywords' => 
      array (
        0 => 'digital music distribution',
        1 => 'ai music distributor',
        2 => 'music distribution service',
        3 => 'artist streaming platform',
        4 => 'automated music marketing',
        5 => 'musicdistro.io',
      ),
      'og_title' => 'MusicDistro.io – AI music distribution for bold artists',
      'og_description' => 'Release your tracks, sync your marketing campaigns and keep your royalties with MusicDistro.io.',
      'twitter_title' => 'MusicDistro.io – AI music distribution for bold artists',
      'twitter_description' => 'Release your tracks, sync your marketing campaigns and keep your royalties with MusicDistro.io.',
      'structured' => 
      array (
        'service_type' => 'Digital music distribution powered by AI',
        'area_served' => 'Worldwide',
        'offers' => 
        array (
          0 => 
          array (
            '@type' => 'Offer',
            'name' => 'Worldwide distribution',
            'price' => '0.00',
            'priceCurrency' => 'EUR',
            'availability' => 'https://schema.org/InStock',
          ),
          1 => 
          array (
            '@type' => 'Offer',
            'name' => 'AI marketing campaigns',
            'price' => '29.00',
            'priceCurrency' => 'EUR',
            'availability' => 'https://schema.org/PreOrder',
          ),
        ),
      ),
    ),
    'nav' => 
    array (
      'brand_aria' => 'Back to the MusicDistro introduction',
      'toggle_open' => 'Open navigation menu',
      'toggle_close' => 'Close navigation menu',
      'menu_heading' => 
      array (
        'badge' => 'Navigation',
        'title' => 'Explore the MusicDistro.io ecosystem',
        'description' => 'Distribution, marketing and analytics tools to support your upcoming releases.',
      ),
      'links' => 
      array (
        'mission' => 'Our mission',
        'features' => 'Features',
        'ai' => 'MusicPulse AI',
        'faq' => 'FAQ',
      ),
      'cta' => 
      array (
        'register' => 'Create an account',
        'login' => 'Sign in',
        'dashboard' => 'Dashboard',
      ),
      'meta' => 
      array (
        'availability' => 'Available 24/7',
        'contact' => 'contact@musicdistro.io',
      ),
    ),
    'hero' => 
    array (
      'eyebrow' => 'THE FIRST AI-POWERED MUSIC DISTRIBUTION',
      'typewriter_phrases' => 
      array (
        0 => 'Release your music on every platform.',
        1 => 'Engage your fans and collect every stream.',
        2 => 'AI orchestrates your rise better than a record label!',
      ),
      'subtitle' => 'MusicDistro.io combines frictionless worldwide distribution with growth intelligence to transform every release into a coordinated launch. From the studio to playlists, everything stays synchronised to grow your audience.',
      'cta' => 
      array (
        'primary' => 'Start for free',
        'secondary' => 'Sign in',
      ),
      'card' =>
      array (
        'aria_label' => 'Partner distribution platforms',
        'badge' => 'DSP HUB',
        'title' => 'Your tracks on every platform',
        'subtitle' => 'Sync your releases across major DSPs and social networks with our AI-powered pipeline.',
        'platforms' =>
        array (
          0 => 'Spotify',
          1 => 'Apple Music',
          2 => 'TikTok',
          3 => 'YouTube Music',
          4 => 'Deezer',
          5 => 'Amazon Music',
        ),
        'marquee' =>
        array (
          0 => 'Spotify',
          1 => 'Apple Music',
          2 => 'TikTok',
          3 => 'YouTube Music',
          4 => 'Amazon Music',
          5 => 'Deezer',
          6 => 'Instagram Reels',
          7 => 'Facebook Music',
        ),
      ),
      'highlights' =>
      array (
        0 => 
        array (
          'value' => '250+',
          'description' => 'Partner platforms: Spotify, Apple Music, TikTok, YouTube, Deezer, Amazon Music and more.',
        ),
        1 => 
        array (
          'value' => '0%',
          'description' => 'Of your royalties kept. You own your masters and steer your growth.',
        ),
        2 => 
        array (
          'value' => '48h',
          'description' => 'To validate priority releases thanks to our AI-assisted quality pipeline.',
        ),
      ),
    ),
    'features' => 
    array (
      'title' => 'A complete service built for artists and labels who want to lead',
      'items' => 
      array (
        0 => 
        array (
          'title' => 'Instant worldwide distribution',
          'description' => 'Send your tracks in one click to more than 250 premium platforms. The algorithm checks your metadata and guarantees compliance.',
          'bullets' => 
          array (
            0 => 'Bulk upload with drag and drop',
            1 => 'Fine control over territories and release windows',
            2 => 'Automatic ISRC/UPC generation when needed',
          ),
        ),
        1 => 
        array (
          'title' => 'Tailor-made AI campaigns',
          'description' => 'Our language models craft your playlist pitches, social copy, video scripts and fan emails. Each campaign is calibrated to grow your community.',
          'bullets' => 
          array (
            0 => 'Real-time analysis of inspirations and trends',
            1 => 'Playlist and media suggestions that fit your style',
            2 => 'Automated follow-ups triggered by fan reactions',
          ),
        ),
        2 => 
        array (
          'title' => 'Transparent, real-time royalties',
          'description' => 'Track your revenue by territory, platform and track. Receive alerts when your streams surge and trigger marketing actions in response.',
          'bullets' => 
          array (
            0 => 'Unified dashboard with secure SSO access',
            1 => 'Fast payouts in multiple currencies and recipients',
            2 => 'Accounting exports and partner-ready reports',
          ),
        ),
      ),
    ),
    'ai' => 
    array (
      'title' => 'MusicPulse AI: your command center to accelerate',
      'items' => 
      array (
        0 => 
        array (
          'title' => 'Release scripting',
          'description' => 'MusicPulse analyses your inspirations, social conversations and search peaks to determine the best launch moment.',
        ),
        1 => 
        array (
          'title' => 'Twin fans',
          'description' => 'Identify audiences similar to your current fans and receive collaboration, playlist and media placement suggestions.',
        ),
        2 => 
        array (
          'title' => 'Momentum alerts',
          'description' => 'Get notified when a key playlist adds your track, when a territory accelerates or when UGC content goes viral.',
        ),
      ),
    ),
    'testimonials' => 
    array (
      'title' => 'They turned their releases into visible wins',
      'prev' => 'Previous testimonial',
      'next' => 'Next testimonial',
      'dots_aria' => 'Testimonials pagination',
      'dot_label' => 'Show testimonial :number',
      'items' => 
      array (
        0 => 
        array (
          'quote' => '“Thanks to MusicDistro.io I grew my audience by 60% in three months compared to my previous distributor. The AI recommendations targeted the right local playlists.”',
          'author' => 'NAOMI LYS – R&B singer',
        ),
        1 => 
        array (
          'quote' => '“People talk, but AI changed everything and accelerated my career with MusicDistro.io. Every release is scripted and my streams explode from day one.”',
          'author' => 'DJ NOVEL – Electronic producer',
        ),
        2 => 
        array (
          'quote' => '“Switching from our historic distributor tripled our pre-saves. MusicDistro.io’s automated sequences keep fans on edge until the drop.”',
          'author' => 'BLACKWAVE COLLECTIVE – Indie label',
        ),
        3 => 
        array (
          'quote' => '“Playlist pitches generated by MusicPulse landed us on five editorial playlists during the first week without hiring extra PR.”',
          'author' => 'LINA ORTEGA – Manager',
        ),
        4 => 
        array (
          'quote' => '“The real-time dashboard lets me adjust every sponsorship campaign before the day ends. We convert twice as many pre-orders as last year.”',
          'author' => 'MANELI CRUZ – Artist manager',
        ),
        5 => 
        array (
          'quote' => '“Our AI TikTok campaigns generate 40% more UGC content. MusicDistro.io replaces three marketing tools we used to pay separately.”',
          'author' => 'PULSEWAVE RECORDS – Independent label',
        ),
        6 => 
        array (
          'quote' => '“MusicDistro.io orchestrated a worldwide surprise drop in 48 hours with multilingual storytelling. Our global community mobilised instantly.”',
          'author' => 'AYA NOVA – Indie pop artist',
        ),
        7 => 
        array (
          'quote' => '“The competitive analysis module helped us pick the right features and triple our monthly plays on Spotify and Deezer.”',
          'author' => 'ORION ATLAS – Alternative pop duo',
        ),
        8 => 
        array (
          'quote' => '“Momentum alerts notify me the minute a key playlist adds my track. I re-engage fans within the hour and keep the hype effortlessly.”',
          'author' => 'KEZZA – Rapper',
        ),
        9 => 
        array (
          'quote' => '“With the AI recommendations we opened two international markets and doubled our online merch revenue in six months.”',
          'author' => 'STELLAR SOUND – Boutique label',
        ),
      ),
    ),
    'faq' => 
    array (
      'title' => 'Frequently asked questions',
      'entries' => 
      array (
        0 => 
        array (
          'question' => 'What is MusicDistro.io?',
          'answer' => 'MusicDistro.io is an AI-powered digital music distribution platform. It delivers your tracks to more than 250 platforms while automating your marketing campaigns and tracking royalties in real time.',
        ),
        1 => 
        array (
          'question' => 'How does MusicDistro.io’s AI work?',
          'answer' => 'Our engine analyses your metadata, inspirations and global trends to suggest release strategies, generate promotional copy, identify targeted playlists and trigger alerts whenever your audience reacts.',
        ),
        2 => 
        array (
          'question' => 'How much does distribution with MusicDistro.io cost?',
          'answer' => 'Registration is free. We operate with transparent revenue sharing, keep 0% of your royalties and offer optional premium boosts to accelerate promotion or unlock advanced analytics.',
        ),
        3 => 
        array (
          'question' => 'Can I connect my existing catalogue?',
          'answer' => 'Yes. MusicDistro.io supports existing catalogues via CSV import, partner integrations and API sync. You keep your ISRC/UPC identifiers and streaming history.',
        ),
        4 => 
        array (
          'question' => 'What support do you offer labels and managers?',
          'answer' => 'Beyond distribution we provide multi-artist reporting tools, trend alerts, sponsored acquisition campaigns and dedicated multilingual support for professionals.',
        ),
      ),
    ),
    'cta' => 
    array (
      'title' => 'Ready to lead the charts?',
      'body' => 'Join MusicDistro.io, activate your account in minutes and let AI propel your music to the fans who are waiting for you.',
      'primary' => 'Create my account',
      'secondary' => 'Access my space',
      'image_alt' => 'Illustration of a global chart climb',
    ),
    'footer' => '© :year :site – The AI distribution that grows your audience.',
  ),
  'tutorial' => 
  array (
    'title' => 'Tutorial: distribute your music – :site',
    'header' => 
    array (
      'title' => 'Distribute your music with confidence',
      'subtitle' => 'This tutorial guides you from the final mix to platform approval. Follow each step to publish your tracks with the emotional impact your audience deserves.',
    ),
    'steps' => 
    array (
      0 => 
      array (
        'title' => 'Prepare your masters and visuals',
        'description' => 'Gather your 24-bit / 48 kHz WAV files, export your cover in 3000x3000 px and make sure your ID3 metadata is consistent.',
        'bullets' => 
        array (
          0 => 'Check the spelling of artists and featured guests.',
          1 => 'Use lossless exports (no MP3).',
          2 => 'Prepare your lyrics if you want to sync them.',
        ),
      ),
      1 => 
      array (
        'title' => 'Create your release in Sonosuite',
        'description' => 'Sign in through the dedicated dashboard card. In Sonosuite, click “Create album” and let the assistant guide you.',
        'bullets' => 
        array (
          0 => 'Select the release type: single, EP or album.',
          1 => 'Fill in your label, songwriters/composers and copyright year.',
          2 => 'Upload your tracks and add ISRC/UPC codes if you have them.',
        ),
      ),
      2 => 
      array (
        'title' => 'Optimise your launch strategy',
        'description' => 'Schedule a release date at least 10 days ahead to benefit from Spotify for Artists recommendations. Activate our AI insights to generate a tailor-made media plan.',
        'bullets' => 
        array (
          0 => 'Schedule TikTok, Instagram Reels and YouTube Shorts teasers.',
          1 => 'Plan automated fan emails and playlist pitches.',
          2 => 'Add exclusive territories if you target specific markets.',
        ),
      ),
      3 => 
      array (
        'title' => 'Track your performance',
        'description' => 'Once the release is online, return to Sonosuite to monitor streams, downloads and royalties. Our AI engine will alert you when your songs gain traction.',
        'bullets' => 
        array (
          0 => 'Analyse the playlists that generate the most streams.',
          1 => 'Identify the countries where fans connect first.',
          2 => 'Unlock collaboration recommendations based on audience similarity.',
        ),
      ),
    ),
    'cta' => 
    array (
      'label' => 'Back to dashboard',
      'guest_label' => 'Create an account',
    ),
  ),
  'validation' => 
  array (
    'method_not_allowed' => 'Method not allowed.',
    'json_invalid' => 'Invalid request: unrecognised JSON payload.',
    'auth_required' => 'Authentication is required.',
    'first_name_required' => 'Your first name is required.',
    'last_name_required' => 'Your last name is required.',
    'email_invalid' => 'Please provide a valid email address.',
    'email_required' => 'Your email address is required.',
    'email_exists' => 'An account already exists with this email address. Please sign in or request a new verification link.',
    'email_unverified' => 'Confirm your email before signing in. Check your inbox or contact support.',
    'country_required' => 'Please select a country.',
    'country_invalid' => 'Please select a valid country.',
    'role_required' => 'Please select your professional profile.',
    'business_type_invalid' => 'Select a valid account type.',
    'phone_invalid' => 'Enter a valid phone number (digits, spaces and symbols .()+-).',
    'company_name_required' => 'Enter your company name.',
    'password_required' => 'Your password is required.',
    'password_min' => 'The password must contain at least 8 characters.',
    'password_confirmation' => 'Passwords do not match.',
    'language_invalid' => 'Please choose a language from the list.',
    'currency_invalid' => 'Please choose a valid currency.',
    'credentials_invalid' => 'Invalid credentials. Please try again or create an account.',
    'token_invalid' => 'This token is invalid.',
    'avatar_storage_failed' => 'Unable to prepare the profile picture storage.',
    'avatar_upload_failed' => 'Uploading the picture failed.',
    'avatar_size' => 'The profile picture must not exceed 5 MB.',
    'avatar_format' => 'Allowed formats: JPEG, PNG or WebP.',
    'avatar_save_failed' => 'Unable to save the profile picture.',
    'user_not_found' => 'User not found.',
    'action_missing' => 'Missing action or user identifier.',
    'cannot_self_manage' => 'You cannot perform this action on your own account.',
    'cannot_manage_super_admin' => 'This action is not allowed on a super administrator.',
    'account_deleted' => 'The account has been deleted.',
    'account_blocked' => 'User access blocked.',
    'account_already_blocked' => 'The account is already blocked.',
    'account_unblocked' => 'User access restored.',
    'account_already_active' => 'The account is already active.',
    'unknown_action' => 'Unknown action.',
    'distribution_provider_invalid' => 'Select a valid distribution dashboard.',
    'sonosuite_base_url_invalid' => 'Enter a valid Sonosuite platform URL (https://).',
    'sonosuite_shared_secret_required' => 'Enter the Sonosuite SSO shared secret.',
  ),
);
