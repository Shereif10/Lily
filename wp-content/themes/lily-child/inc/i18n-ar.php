<?php
/**
 * Lily Arabic dictionary — actual Arabic content for the existing English strings.
 *
 * Extends the EXISTING translation architecture (TranslatePress + bilingual
 * "_ar" dashboard fields). It does not replace TranslatePress, does not add a
 * second engine and does not touch RTL/layout.
 *
 * How it works:
 * - All Lily frontend strings already use the "lily" textdomain.
 * - On Arabic requests (TranslatePress /ar/ URL or Arabic locale) this file
 *   returns natural Egyptian-friendly Arabic via the normal WordPress
 *   gettext filters. English requests are untouched.
 * - TranslatePress entries always win when they exist: TranslatePress
 *   rewrites the final HTML after these filters run, so anything the brand
 *   owner translates in TranslatePress → Translation Editor overrides this
 *   fallback automatically.
 * - Dashboard "_ar" values always win over this dictionary: lily_get_option()
 *   / lily_ml_value() / lily_nav_get_option() check the saved Arabic field
 *   first and only consult this dictionary when Arabic is still blank.
 *
 * Brand tone: elegant, calm, premium, feminine, natural. Modern Standard
 * Arabic. No medical claims, no meaning changes.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Central English → Arabic map for every existing Lily frontend string.
 *
 * Keys must match the English source strings EXACTLY (including punctuation).
 *
 * @return array
 */
function lily_ar_dictionary() {
	static $map = null;

	if ( null !== $map ) {
		return $map;
	}

	$map = array(
		// ── Header / global ──────────────────────────────────────────
		'Skip to content'              => 'تخطي إلى المحتوى',
		'Primary navigation'           => 'التنقل الرئيسي',
		'Search'                       => 'بحث',
		'Search lenses, brands...'     => 'ابحثي عن العدسات والماركات...',
		'Open menu'                    => 'فتح القائمة',
		'Close menu'                   => 'إغلاق القائمة',
		'Open Menu'                    => 'فتح القائمة',
		'Close Menu'                   => 'إغلاق القائمة',
		'Cart'                         => 'السلة',
		'Wishlist'                     => 'المفضلة',
		'Home'                         => 'الرئيسية',
		'Breadcrumb'                   => 'مسار التنقل',
		'Learn more'                   => 'اعرفي المزيد',
		'Announcement'                 => 'إعلان',
		'Our brands'                   => 'ماركاتنا',

		// ── Navigation ───────────────────────────────────────────────
		'Shop'                         => 'تسوقي',
		'Colored Lenses'               => 'عدسات ملونة',
		'Clear Lenses'                 => 'عدسات شفافة',
		'Accessories & Lens Care'      => 'إكسسوارات والعناية بالعدسات',
		'Find My Lenses'               => 'اعثري على عدساتك',
		'Company'                      => 'الشركة',
		'About Us'                     => 'من نحن',
		'About Lily'                   => 'عن ليلي',
		'FAQs'                         => 'الأسئلة الشائعة',
		'Contact Us'                   => 'تواصلي معنا',
		'Contact'                      => 'تواصلي معنا',
		'By Brand'                     => 'حسب الماركة',
		'By Look / Effect'             => 'حسب اللوك والتأثير',
		'By Color'                     => 'حسب اللون',
		'By Prescription'              => 'حسب المقاس',
		'By Duration'                  => 'حسب مدة الاستبدال',
		'Shop All'                     => 'تسوقي الكل',
		'New'                          => 'جديد',
		'Find Your Best Lenses'        => 'اعثري على عدساتك المثالية',
		'Find Your Lenses'             => 'اعثري على عدساتك',

		// ── Announcement bar ─────────────────────────────────────────
		'Fast Delivery'                => 'توصيل سريع',
		'Cash on Delivery'             => 'الدفع عند الاستلام',
		'Premium Quality'              => 'جودة فاخرة',

		// ── Hero ─────────────────────────────────────────────────────
		'Shop Now'                     => 'تسوقي الآن',
		'Lily Contact Lenses'          => 'عدسات ليلي اللاصقة',
		'Featured collections'         => 'تشكيلات مميزة',

		// ── Homepage sections ────────────────────────────────────────
		'Shop by Collection'           => 'تسوقي حسب المجموعة',
		'Shop by Collections'          => 'تسوقي حسب المجموعات',
		'Shop by Colors'               => 'تسوقي حسب اللون',
		'Shop by Color'                => 'تسوقي حسب اللون',
		'Explore Collections'          => 'اكتشفي المجموعات',
		'Find the lens that fits you'  => 'اعثري على العدسة المناسبة لكِ',
		'Explore our collections and choose the style that matches you.' => 'اكتشفي مجموعاتنا واختاري الستايل اللي يعبر عنكِ.',
		'Find the color that expresses you' => 'اعثري على اللون اللي يعبر عنكِ',
		'Discover shades designed to match your unique look.' => 'اكتشفي درجات مصممة لتناسب إطلالتك المميزة.',
		'View All Colors'              => 'عرض كل الألوان',
		'Best Sellers'                 => 'الأكثر مبيعاً',
		'Best-selling products'        => 'المنتجات الأكثر مبيعاً',
		'Previous products'            => 'المنتجات السابقة',
		'Next products'                => 'المنتجات التالية',
		'The shades our customers love most' => 'الدرجات اللي عميلاتنا حبوها أكتر',
		'Discover best-selling colors that suit every look and mood.' => 'اكتشفي الألوان الأكثر مبيعاً اللي تناسب كل إطلالة ومزاج.',
		'Shop Best Sellers'            => 'تسوقي الأكثر مبيعاً',
		'Best Seller'                  => 'الأكثر مبيعاً',
		'Sale'                         => 'خصم',
		'Add to Cart'                  => 'أضيفي إلى السلة',
		'Add to cart'                  => 'أضيفي إلى السلة',
		'Out of Stock'                 => 'نفذت الكمية',
		'Out of stock'                 => 'نفذت الكمية',
		'In Stock'                     => 'متوفر',
		'With Power'                   => 'بمقاس نظر',
		'Without Power'                => 'بدون مقاس نظر',

		// ── Lens Finder intro ────────────────────────────────────────
		'Lens Finder'                  => 'دليل اختيار العدسات',
		'Find your perfect lenses'     => 'اعثري على عدساتك المثالية',
		"Answer a few simple questions and we'll recommend the lenses that are right for you." => 'جاوبي على كام سؤال بسيط وهنرشح لكِ العدسات الأنسب لكِ.',
		'Personalized recommendations' => 'ترشيحات مخصصة لكِ',
		'Tailored picks based on your eyes and style.' => 'اختيارات مناسبة لعينيكِ وستايلك.',
		'RX & non-RX options'          => 'خيارات بمقاس وبدون مقاس',
		'Find lenses that fit your prescription or lifestyle.' => 'اعثري على عدسات تناسب مقاسك أو أسلوب حياتك.',
		'Comfort & style matched for you' => 'راحة وأناقة على مقاسك',
		'Beautiful lenses that feel as good as they look.' => 'عدسات جميلة ومريحة في نفس الوقت.',
		'Secure & private. Your data is safe with us.' => 'آمن وسري. بياناتك في أمان معنا.',
		'Start Lens Finder'            => 'ابدئي دليل العدسات',

		// ── Lens Finder quiz ─────────────────────────────────────────
		'Back'                         => 'رجوع',
		'Next'                         => 'التالي',
		'Review your selections'       => 'راجعي اختياراتك',
		'Finding lenses...'            => 'بندور لكِ على العدسات المناسبة...',
		'I understand these selections are my responsibility and do not replace professional eye care advice.' => 'أتفهم أن هذه الاختيارات مسؤوليتي ولا تغني عن استشارة طبيب العيون.',
		'No matching lenses were found. Try changing one or two selections.' => 'لم نجد عدسات مطابقة. جربي تغيير اختيار أو اختيارين.',
		'Recommended lenses'           => 'العدسات المرشحة لكِ',
		'Please confirm your selections before continuing.' => 'من فضلك أكدي اختياراتك قبل المتابعة.',
		'Choose one option to continue.' => 'اختاري إجابة واحدة للمتابعة.',
		'Lens Type'                    => 'نوع العدسة',
		'Prescription'                 => 'المقاس',
		'Preferred Look'               => 'اللوك المفضل',
		'Natural Eye Color'            => 'لون عينك الطبيعي',
		'Skin Tone'                    => 'لون البشرة',
		'Preferred Color'              => 'اللون المفضل',
		'Replacement Duration'         => 'مدة الاستبدال',
		'Fair'                         => 'فاتحة جداً',
		'Light / Medium'               => 'فاتحة إلى متوسطة',
		'Medium'                       => 'متوسطة',
		'Tan'                          => 'قمحية',
		'Deep'                         => 'داكنة',
		'Brown'                        => 'بني',
		'Hazel'                        => 'عسلي',
		'Green'                        => 'أخضر',
		'Blue'                         => 'أزرق',
		'Gray'                         => 'رمادي',
		'Other'                        => 'أخرى',
		'Tell us about you'            => 'احكيلنا عنكِ',
		'Choose your preferences and lens needs.' => 'اختاري تفضيلاتك واحتياجاتك من العدسات.',
		'Find your match'              => 'اعثري على المناسب لكِ',
		'We narrow down the options that fit you best.' => 'بنختار لكِ الأنسب من بين الخيارات.',
		'Explore your shades'          => 'اكتشفي درجاتك',
		'See the colors and styles that suit you.' => 'شوفي الألوان والستايلات اللي تناسبك.',
		'Choose your lenses'           => 'اختاري عدساتك',
		'Pick your favorite and shop with confidence.' => 'اختاري المفضل لكِ وتسوقي بثقة.',
		'View Product'                 => 'عرض المنتج',
		'The Lens Finder session expired. Please refresh the page and try again.' => 'انتهت جلسة دليل العدسات. حدثي الصفحة وحاولي مرة أخرى.',
		'WooCommerce is required for Lens Finder recommendations.' => 'يتطلب دليل العدسات تفعيل ووكومرس لعرض الترشيحات.',

		// ── About page ───────────────────────────────────────────────
		'About Lily'                   => 'عن ليلي',
		'Beauty that feels naturally yours.' => 'جمال طبيعي يحسسك إنكِ على طبيعتك.',
		'Our Story'                    => 'حكايتنا',
		'Lily was created around one simple idea — that changing your look does not have to mean changing who you are.' => 'اتعملت ليلي لفكرة بسيطة — إن تغيير إطلالتك مش معناه تغيري نفسك.',
		'Choosing a colored lens can feel overwhelming, so we keep everything calm and clear: honest shades, natural-looking results and guidance that feels personal.' => 'اختيار العدسة الملونة ممكن يكون محير، عشان كده بنخلي كل حاجة هادية وواضحة: درجات صادقة، ونتائج طبيعية، ونصايح تحسي إنها معمولة لكِ.',
		'Every collection is edited with care, so you can compare, explore and find the color that feels like you.' => 'كل مجموعة مختارة بعناية، عشان تقارني وتستكشفي وتلاقي اللون اللي شبهك.',
		'Our Mission'                  => 'مهمتنا',
		'To make self-expression feel effortless — through colors that complement the way you see yourself.' => 'نخلي التعبير عن نفسك سهل — بألوان تكمل صورتك عن نفسك.',
		'Our Vision'                   => 'رؤيتنا',
		'A world where beauty feels personal, natural, and entirely your own.' => 'عالم يكون فيه الجمال شخصي وطبيعي وبتاعك بالكامل.',
		'We want Lily to be the place you return to whenever you feel like a refresh — a quiet, trusted destination for discovering your next look.' => 'عايزين ليلي تكون المكان اللي ترجعيله كل ما تحبي تجددي — وجهة هادية وموثوقة لاكتشاف إطلالتك الجاية.',
		'Our Approach'                 => 'أسلوبنا',
		'01'                           => '٠١',
		'02'                           => '٠٢',
		'03'                           => '٠٣',
		'Natural'                      => 'طبيعي',
		'Designed to complement, not overpower.' => 'مصممة لتكمل جمالك مش لتغطي عليه.',
		'Confidence'                   => 'ثقة',
		'A subtle change can change the way you feel.' => 'تغيير بسيط ممكن يغير إحساسك بنفسك.',
		'Quality'                      => 'جودة',
		'Created with care for everyday comfort and beauty.' => 'معمولة بعناية لراحة وجمال كل يوم.',
		'Find Your Perfect Lens'        => 'اعثري على عدستك المثالية',
		'Discover the color that feels like you.' => 'اكتشفي اللون اللي شبهك.',
		'Shop Lenses'                  => 'تسوقي العدسات',
		'Lily editorial'               => 'من ليلي',

		// ── FAQ ──────────────────────────────────────────────────────
		'Frequently asked questions about colored contact lenses — choosing the right color, care, and everyday use.' => 'الأسئلة الأكثر شيوعاً عن العدسات اللاصقة الملونة — اختيار اللون والعناية والاستخدام اليومي.',
		'How do I choose the right lens color for my skin tone and eye color?' => 'إزاي أختار لون العدسة المناسب لبشرتي ولون عيني؟',
		'Warm or olive skin tones suit honey, olive green, and warm brown shades. Fair skin tones suit gray, blue, and emerald green. If your natural eye color is dark, choose colors with stronger pigmentation to cover the original color more clearly.' => 'البشرة الدافئة أو القمحية يناسبها العسلي والأخضر الزيتي والبني الدافئ. والبشرة الفاتحة يناسبها الرمادي والأزرق والأخضر الزمردي. ولو لون عينك الطبيعي غامق، اختاري ألوان بتغطية أقوى عشان يبان اللون بوضوح.',
		'What is the difference between limbal-ring lenses and lenses without a defined rim?' => 'إيه الفرق بين العدسات المحددة والعدسات بدون تحديد؟',
		'Defined lenses have a dark ring around the outer edge that makes the eyes look larger and more defined, while lenses without a defined rim give a more natural look that blends with your original eye color without changing the appearance of the pupil size.' => 'العدسات المحددة فيها إطار غامق حوالين الحرف بيكبر العين ويبرزها، بينما العدسات بدون تحديد بتدي لوك طبيعي مندمج مع لون عينك الأصلي من غير ما يغير شكل حدقة العين.',
		'How do I take care of my lenses and protect them from damage?' => 'إزاي أعتني بالعدسات وأحميها من التلف؟',
		'Wash and dry your hands thoroughly before touching your lenses. Use a sterile contact-lens solution specifically intended for cleaning them daily, and never wash the lenses or their case with tap water. It is recommended to replace the lens case every 1 to 3 months.' => 'اغسلي إيدك ونشفيها كويس قبل ما تلمسي العدسات. استخدمي محلول عدسات معقم مخصص للتنظيف اليومي، ومتغسليش العدسات ولا العلبة بمية الحنفية أبداً. ويُفضل تغيري علبة العدسات كل شهر إلى ٣ شهور.',
		'How many hours can I wear contact lenses per day?' => 'كام ساعة أقدر ألبس العدسات في اليوم؟',
		'It is recommended to wear contact lenses for approximately 6 to 8 hours per day to help avoid eye fatigue or dryness. They should be removed immediately before sleeping or swimming.' => 'يُفضل تلبسي العدسات من ٦ إلى ٨ ساعات في اليوم عشان تتجنبي إجهاد أو جفاف العين. ولازم تخلعيها فوراً قبل النوم أو السباحة.',
		'What should I do if I feel burning or a sharp sensation while wearing my lenses?' => 'لو حسيت بحرقان أو شكة وأنا لابسة العدسة أعمل إيه؟',
		'Remove the lens immediately and inspect it for any debris or cracks. Clean it thoroughly with contact-lens solution and make sure it is not inside out before putting it back in. If the irritation continues, allow your eye to rest and consult a doctor.' => 'اخلعي العدسة فوراً وافحصيها للتأكد إن مفيهاش شوائب أو شروخ. نضفيها كويس بالمحلول وتأكدي إنها مش مقلوبة قبل ما تلبسيها تاني. ولو التهيج استمر، ريحي عينك واستشيري طبيب.',
		'How can I tell if my lens is inside out?' => 'إزاي أعرف إن العدسة مقلوبة ولا معدولة؟',
		'Place the lens on the tip of your finger. If the edges point upward like a regular cup or "U" shape, the lens is positioned correctly. If the edges flare outward like a flat plate, the lens is inside out and should be turned over.' => 'حطي العدسة على طرف صباعك؛ لو أطرافها لفوق على شكل كوب أو حرف U تبقى معدولة. ولو الأطراف مفرودة لبره زي الطبق تبقى مقلوبة ولازم تعدليها.',
		'Can I sleep while wearing contact lenses?' => 'هل ينفع أنام وأنا لابسة العدسات؟',
		'Sleeping while wearing contact lenses is not recommended, as it can reduce oxygen reaching the cornea and may cause severe dryness or bacterial infections.' => 'النوم بالعدسات غير مُنصح به، لأنه بيقلل الأكسجين اللي واصل للقرنية وممكن يسبب جفاف شديد أو التهابات.',

		// ── Contact page ─────────────────────────────────────────────
		'We are here for you'          => 'احنا هنا عشانك',
		'We are here to help with your lenses, orders, or anything else you need.' => 'احنا هنا لمساعدتك في العدسات أو الطلبات أو أي حاجة تحتاجيها.',
		'Contact information and form' => 'بيانات التواصل ونموذج الرسائل',
		'Thank you! Your message has been sent. Our team will get back to you as soon as possible.' => 'شكراً لكِ! وصلتنا رسالتك وفريقنا هيرد عليكِ في أقرب وقت.',
		'Something went wrong — please check your details and try again.' => 'حصلت مشكلة — راجعي بياناتك وحاولي مرة تانية.',
		'Get in Touch'                 => 'تواصلي معنا',
		'Phone'                        => 'التليفون',
		'Email'                        => 'البريد الإلكتروني',
		'Chat with us'                 => 'كلمينا واتساب',
		'Our team is ready to help you with any questions about our lenses, orders, or eye care. We aim to respond as soon as possible.' => 'فريقنا جاهز يساعدك في أي سؤال عن العدسات أو الطلبات أو العناية بالعين. وبنحاول نرد عليكِ في أسرع وقت.',
		'Send Us a Message'            => 'ابعتيلنا رسالة',
		'Leave this field empty'       => 'سيبي الخانة دي فاضية',
		'Full Name'                    => 'الاسم بالكامل',
		'Phone Number'                 => 'رقم التليفون',
		'Email Address'                => 'البريد الإلكتروني',
		'Subject'                      => 'الموضوع',
		'Message'                      => 'الرسالة',
		'Send Message'                 => 'إرسال الرسالة',
		'Need help finding your lenses?' => 'محتاجة مساعدة في اختيار عدساتك؟',
		'Let us help you find the right pair for your look.' => 'خلينا نساعدك تلاقي العدسة المناسبة لإطلالتك.',

		// ── Shipping policy ──────────────────────────────────────────
		'Shipping Policy'              => 'سياسة الشحن',
		'Shipping & Delivery Policy'   => 'سياسة الشحن والتوصيل',
		'Everything you need to know about delivery with Lily.' => 'كل اللي محتاجة تعرفيه عن التوصيل مع ليلي.',
		'Lily currently delivers within Egypt. This page explains how delivery works — the exact fee and details for your order are always confirmed during checkout.' => 'ليلي بتوصل حالياً داخل مصر. الصفحة دي بتشرح نظام التوصيل — وسعر الشحن وتفاصيل طلبك بتتأكد دايماً أثناء إتمام الطلب.',
		'Shipping & Delivery'          => 'الشحن والتوصيل',
		'Lily currently delivers within Egypt. Orders are prepared after confirmation, and our team makes sure every parcel leaves carefully packed and ready for its journey to you.' => 'ليلي بتوصل حالياً داخل مصر. الطلبات بتتجهز بعد التأكيد، وفريقنا بيتأكد إن كل طرد خارج متغلف بعناية وجاهز لرحلته لحد عندك.',
		'Delivery Areas'               => 'مناطق التوصيل',
		'At checkout you choose your Governorate / Area from the delivery areas we support across Egypt. Some groups cover several detailed locations, which are shown inside the area selection to help you pick correctly.' => 'أثناء إتمام الطلب بتختاري المحافظة / المنطقة من مناطق التوصيل المتاحة في مصر. بعض المجموعات بتغطي عدة أماكن بالتفصيل، وبتظهر جوّه اختيار المنطقة عشان تختاري صح.',
		'Delivery Times'               => 'مواعيد التوصيل',
		'Order preparation and delivery timing depend on your area and order details. Any timing shared during checkout or order confirmation applies — if anything is unclear, just ask us before ordering.' => 'وقت تجهيز وتوصيل الطلب بيعتمد على منطقتك وتفاصيل طلبك. أي ميعاد يتقالك أثناء إتمام الطلب أو تأكيده هو اللي بيمشي — ولو في حاجة مش واضحة اسألينا قبل ما تطلبي.',
		'Shipping Fees'                => 'مصاريف الشحن',
		'Shipping fees are calculated based on the selected delivery area. The exact fee is always shown during checkout before you place your order.' => 'مصاريف الشحن بتتحسب حسب منطقة التوصيل المختارة. وسعر الشحن بالظبط بيظهر دايماً أثناء إتمام الطلب قبل ما تأكدي طلبك.',
		'How Your Order Is Delivered'  => 'طلبك بيوصلك إزاي',
		'Please provide a detailed address — area, street, building, floor, apartment and a nearby landmark — along with a reachable phone number so the courier can find you easily. If your delivery is delayed or something looks wrong, contact us with your order number and we will follow up with the courier.' => 'من فضلك اكتبي عنوان تفصيلي — المنطقة والشارع والعمارة والدور والشقة وعلامة مميزة قريبة — مع رقم تليفون متاح عشان المندوب يوصلك بسهولة. ولو التوصيل اتأخر أو في حاجة غلط، تواصلي معنا برقم الطلب وهنتابع مع شركة الشحن.',
		'Important Notes'              => 'ملاحظات مهمة',
		'Please double-check your address before ordering. If you notice a mistake after ordering, contact us immediately and we will do our best to update it before dispatch. If an item arrived damaged or incorrect, reach out as soon as possible with photos and your order number.' => 'راجعي عنوانك كويس قبل ما تطلبي. ولو لاحظتي غلطة بعد الطلب، تواصلي معنا فوراً وهنحاول نعدلها قبل الشحن. ولو وصلك منتج تالف أو غلط، تواصلي معنا في أقرب وقت بالصور ورقم الطلب.',
		'Need more help?'              => 'محتاجة مساعدة أكتر؟',
		'Have a question about your delivery?' => 'عندك سؤال عن التوصيل؟',

		// ── Returns & Exchange ───────────────────────────────────────
		'Returns & Exchange'           => 'الاسترجاع والاستبدال',
		'Returns & Exchange Policy'    => 'سياسة الاستبدال والاسترجاع',
		'Your satisfaction is our priority. Please read our returns and exchange policy carefully.' => 'رضاكِ أولويتنا. من فضلك اقرئي سياسة الاسترجاع والاستبدال بعناية.',
		'Returns and exchange policy details' => 'تفاصيل سياسة الاسترجاع والاستبدال',
		'Eligibility for Returns'      => 'شروط الاسترجاع',
		'We handle every return request fairly and clearly. Because contact lenses are personal-care products, returns follow strict rules — if you believe your order qualifies, contact us with your order number and we will review it with you.' => 'بنتعامل مع كل طلب استرجاع بعدل ووضوح. ولأن العدسات من منتجات العناية الشخصية، فالاسترجاع ليه قواعد صارمة — لو شايفة إن طلبك مستوفي الشروط، تواصلي معنا برقم الطلب وهنراجعه معاكِ.',
		'Non-Returnable Items'         => 'المنتجات غير القابلة للاسترجاع',
		'Products that have been opened or used usually cannot be accepted back for hygiene reasons and are generally non-refundable, except where required by law or where the product reached you faulty.' => 'المنتجات اللي اتفتحت أو اتستخدمت عادة لا يمكن قبولها لأسباب صحية وتكون غير قابلة للاسترداد، إلا لو القانون بيلزم بكده أو لو المنتج وصلك فيه عيب.',
		'Eligibility for Exchange'     => 'شروط الاستبدال',
		'Would you prefer a different shade? Contact us with your order number and the details of the item you would like to exchange, and we will let you know what is possible for your order.' => 'حابة درجة مختلفة؟ تواصلي معنا برقم الطلب وتفاصيل المنتج اللي عايزة تستبدليه وهنقولك إيه المتاح لطلبك.',
		'Conditions'                   => 'الشروط',
		'Returned items are inspected before a final decision is made. Please keep the product in the condition you received it and share clear photos where requested so our team can assess your request quickly.' => 'المنتجات المرتجعة بتتفحص قبل القرار النهائي. من فضلك حافظي على المنتج بنفس حالته اللي وصلك بيها وابعتي صور واضحة لو اتطلب منك عشان فريقنا يقيم طلبك بسرعة.',
		'How to Request'               => 'إزاي تطلبي',
		'Contact us by phone or through the contact page with your order number and a short description of your request. If an item arrived damaged or you received the wrong product, reach out as soon as possible with photos and your order number.' => 'تواصلي معنا بالتليفون أو من صفحة التواصل برقم الطلب ووصف مختصر لطلبك. ولو وصلك منتج تالف أو غلط، تواصلي معنا في أقرب وقت بالصور ورقم الطلب.',
		'Refunds'                      => 'الاسترداد',
		'Where a return is accepted, any refund is issued for the value of the returned item as approved by our team. Please see our Refund Policy for how refunds work after a return is accepted.' => 'لو تم قبول الاسترجاع، بيتم رد قيمة المنتج المرتجع حسب ما يعتمده فريقنا. شوفي سياسة الاسترداد لمعرفة نظام رد المبلغ بعد قبول الاسترجاع.',
		'Have a question about returns or exchange?' => 'عندك سؤال عن الاسترجاع أو الاستبدال؟',

		// ── Terms & Conditions ───────────────────────────────────────
		'Terms & Conditions'           => 'الشروط والأحكام',
		'Please read these terms carefully before using Lily.' => 'من فضلك اقرئي هذه الشروط بعناية قبل استخدام ليلي.',
		'Terms and conditions details' => 'تفاصيل الشروط والأحكام',
		'General Terms'                => 'شروط عامة',
		'By using this website or placing an order you agree to these terms. Please read them before ordering — if anything is unclear, contact us and we will gladly explain.' => 'باستخدامك الموقع أو بطلبك من عليه فأنتِ توافقين على هذه الشروط. من فضلك اقرئيها قبل الطلب — ولو في حاجة مش واضحة تواصلي معنا وهنشرحها لكِ بكل سرور.',
		'Products & Information'       => 'المنتجات والمعلومات',
		'We describe our products as accurately as possible. Colours may appear slightly different depending on your screen. Product descriptions are for general guidance and are not medical advice.' => 'بنوصف منتجاتنا بأدق شكل ممكن. الألوان ممكن تظهر مختلفة شوية حسب شاشتك. ووصف المنتجات للاسترشاد العام وليس نصيحة طبية.',
		'Orders & Payment'             => 'الطلبات والدفع',
		'Submitting an order is an offer to purchase. We may contact you to confirm details before dispatch, and orders may be declined if information is incomplete or stock is unavailable. Payment is made through the options presented at checkout.' => 'تقديم الطلب يعتبر عرضاً للشراء. ممكن نتواصل معاكِ لتأكيد التفاصيل قبل الشحن، والطلب ممكن يترفض لو البيانات ناقصة أو المنتج غير متوفر. والدفع بيتم من خلال الطرق المتاحة أثناء إتمام الطلب.',
		'Pricing & Availability'       => 'الأسعار والتوفر',
		'Prices are shown in Egyptian Pounds (EGP) and include the totals displayed at checkout. If a product becomes unavailable after you order, we will contact you about the available options.' => 'الأسعار بالجنيه المصري وتشمل الإجمالي الظاهر أثناء إتمام الطلب. ولو منتج بقى غير متوفر بعد طلبك، هنتواصل معاكِ بخصوص الخيارات المتاحة.',
		'User Responsibilities'        => 'مسؤوليات العميلة',
		'You are responsible for providing accurate delivery information and for using products according to the guidance on their packaging. When ordering powered lenses you are responsible for selecting the correct power for your right and left eye — if you are unsure, please consult your eye-care specialist before ordering.' => 'أنتِ مسؤولة عن تقديم بيانات توصيل صحيحة وعن استخدام المنتجات حسب الإرشادات المكتوبة على العبوة. وعند طلب عدسات بمقاس أنتِ مسؤولة عن اختيار المقاس الصحيح للعين اليمين والشمال — ولو مش متأكدة استشيري طبيب العيون قبل الطلب.',
		'Intellectual Property'        => 'الملكية الفكرية',
		'All content, branding and imagery on this website belong to Lily or its licensors. Please do not copy, reproduce or use our content without permission.' => 'كل المحتوى والعلامة والصور في الموقع ملك ليلي أو الجهات المرخصة لها. من فضلك متنقليش أو تستخدمي المحتوى بدون إذن.',
		'Limitation of Liability'      => 'حد المسؤولية',
		'Detailed liability terms are still being finalised and will be published here. In the meantime, if something goes wrong with your order or experience, please contact us and we will do our best to make it right.' => 'بنود المسؤولية التفصيلية لسه بتتجهز وهتتنشر هنا. وفي الوقت الحالي، لو حصلت مشكلة في طلبك أو تجربتك، تواصلي معنا وهنعمل أقصى ما عندنا عشان نرضيكِ.',
		'Changes to These Terms'       => 'تعديل هذه الشروط',
		'We may update these terms from time to time; the current version will always appear on this page. Questions about these terms? Contact us and we will be happy to help.' => 'ممكن نحدث هذه الشروط من وقت للتاني؛ والنسخة الحالية هتكون دايماً في الصفحة دي. عندك أسئلة عن الشروط؟ تواصلي معنا وهنكون سعداء بمساعدتك.',
		'If you have any questions about these terms, we are here to help.' => 'لو عندك أي أسئلة عن هذه الشروط، احنا هنا لمساعدتك.',

		// ── Refund policy ────────────────────────────────────────────
		'Refund Policy'                => 'سياسة الاسترداد',
		'Our goal is your satisfaction. Please read our refund policy carefully.' => 'هدفنا رضاكِ. من فضلك اقرئي سياسة الاسترداد بعناية.',
		'Refund policy details'        => 'تفاصيل سياسة الاسترداد',
		'Eligibility for Refunds'      => 'شروط الاسترداد',
		'We want every customer to be satisfied. If something went wrong with your order, you may request a refund — because contact lenses are personal-care products, eligibility rules are strict and every request is reviewed individually.' => 'يهمّنا رضا كل عميلة. لو حصلت مشكلة في طلبك ممكن تطلبي استرداد — ولأن العدسات من منتجات العناية الشخصية، فالشروط صارمة وكل طلب بيتراجع لوحده.',
		'Non-Refundable Items'         => 'المنتجات غير القابلة للاسترداد',
		'Opened or used lenses generally cannot be resold and are therefore generally non-refundable, except where required by law or where the product reached you faulty.' => 'العدسات المفتوحة أو المستخدمة عادة لا يمكن إعادة بيعها وبالتالي تكون غير قابلة للاسترداد، إلا لو القانون بيلزم بكده أو لو المنتج وصلك فيه عيب.',
		'How to Request a Refund'      => 'إزاي تطلبي استرداد',
		'Contact us by phone or through the contact page with your order number and a short description of the issue, along with photos if the product arrived damaged or incorrect.' => 'تواصلي معنا بالتليفون أو من صفحة التواصل برقم الطلب ووصف مختصر للمشكلة، مع صور لو المنتج وصلك تالف أو غلط.',
		'Refund Process'               => 'خطوات الاسترداد',
		'Returned items are inspected before any refund decision is made. Our team reviews your request and the item condition, then confirms whether a refund is approved.' => 'المنتجات المرتجعة بتتفحص قبل أي قرار بالاسترداد. فريقنا بيراجع طلبك وحالة المنتج، وبعدين يأكد إذا كان الاسترداد مقبول ولا لأ.',
		'Refund Methods'               => 'طرق الاسترداد',
		'Once a refund is approved, our team confirms the refund method with you directly. Final refund-method details are still being confirmed and will be published here.' => 'بعد قبول الاسترداد، فريقنا بيأكد معاكِ طريقة رد المبلغ مباشرة. وتفاصيل طرق الاسترداد النهائية لسه بتتجهز وهتتنشر هنا.',
		'Refund Timeline'              => 'مدة الاسترداد',
		'Refund timing is confirmed with you when your refund is approved. Final refund timeframes are still being confirmed and will be published here — if you are waiting on a refund, contact us with your order number.' => 'مدة الاسترداد بتتأكد معاكِ عند قبول طلبك. والمدد النهائية لسه بتتجهز وهتتنشر هنا — ولو مستنية استرداد تواصلي معنا برقم الطلب.',
		'If your order arrived damaged or you received the wrong item, contact us as soon as possible with photos and your order number so we can make it right.' => 'لو طلبك وصلك تالف أو استلمتي منتج غلط، تواصلي معنا في أقرب وقت بالصور ورقم الطلب عشان نصلح الموقف.',
		'Have a question about your refund?' => 'عندك سؤال عن الاسترداد؟',

		// ── Cookie policy ────────────────────────────────────────────
		'Cookie Policy'                => 'سياسة ملفات الارتباط',
		'We use cookies to enhance your browsing experience on Lily. Please read this policy carefully.' => 'بنستخدم ملفات الارتباط لتحسين تجربة تصفحك في ليلي. من فضلك اقرئي هذه السياسة بعناية.',
		'Cookie policy details'        => 'تفاصيل سياسة ملفات الارتباط',
		'What Are Cookies?'            => 'ما هي ملفات الارتباط؟',
		'Cookies are small files stored by your browser that help websites remember information between pages and visits.' => 'ملفات الارتباط هي ملفات صغيرة بيخزنها المتصفح بتاعك عشان تساعد المواقع تفتكر المعلومات بين الصفحات والزيارات.',
		'How We Use Cookies'           => 'إزاي بنستخدم ملفات الارتباط',
		'We use cookies to keep the store working correctly, to remember your cart and preferences, and to understand how the website is used.' => 'بنستخدم ملفات الارتباط عشان المتجر يشتغل صح، وعشان نفتكر السلة وتفضيلاتك، ونفهم إزاي الموقع بيُستخدم.',
		'Types of Cookies We Use'      => 'أنواع ملفات الارتباط اللي بنستخدمها',
		'Essential cookies are required for basic functions such as cart sessions and checkout — the store cannot work properly without them. Preference cookies remember choices such as language to make your visit smoother.' => 'ملفات الارتباط الأساسية مطلوبة للوظائف الأساسية زي جلسات السلة وإتمام الطلب — والمتجر مش هيشتغل صح من غيرها. وملفات التفضيلات بتفتكر اختياراتك زي اللغة عشان زيارتك تبقى أسهل.',
		'Third-Party Cookies'          => 'ملفات ارتباط الطرف الثالث',
		'This website runs on the standard platforms and services needed to operate an online store. We do not sell your personal data. If any third-party service sets its own cookies, those cookies are governed by that service’s own policy.' => 'الموقع شغال على المنصات والخدمات الأساسية اللازمة لتشغيل متجر إلكتروني. ومش بنبيع بياناتك الشخصية. ولو أي خدمة خارجية حطت ملفات ارتباط خاصة بيها، فهي تخضع لسياسة الخدمة دي.',
		'Managing Cookies'             => 'إدارة ملفات الارتباط',
		'You can control or delete cookies through your browser settings. Blocking some cookies may affect how the store works — for example, your cart may not be remembered between visits.' => 'تقدري تتحكمي أو تمسحي ملفات الارتباط من إعدادات المتصفح. وحظر بعض الملفات ممكن يأثر على شغل المتجر — على سبيل المثال سلتك ممكن متتحفظش بين الزيارات.',
		'Your Consent'                 => 'موافقتك',
		'Essential cookies are required for the store to function. For anything beyond what is strictly necessary, you can manage your choices at any time through your browser settings described above.' => 'ملفات الارتباط الأساسية مطلوبة عشان المتجر يشتغل. وأي حاجة زيادة عن الضروري تقدري تديري اختياراتك فيها في أي وقت من إعدادات المتصفح المذكورة فوق.',
		'Changes to This Policy'       => 'تعديل هذه السياسة',
		'We may update this policy from time to time; the current version will always appear on this page.' => 'ممكن نحدث هذه السياسة من وقت للتاني؛ والنسخة الحالية هتكون دايماً في الصفحة دي.',
		'Have a question about cookies?' => 'عندك سؤال عن ملفات الارتباط؟',

		// ── Wishlist ─────────────────────────────────────────────────
		'My Wishlist'                  => 'مفضلتي',
		'Your favorite Lily lenses, saved for later.' => 'عدسات ليلي المفضلة عندك، محفوظة لوقت تاني.',
		'Saved products'               => 'المنتجات المحفوظة',
		'Your Wishlist is Empty'       => 'قائمة مفضلتك فاضية',
		'Save your favorite lenses and find them here anytime.' => 'احفظي عدساتك المفضلة وهتلاقيها هنا في أي وقت.',
		'Add to wishlist'              => 'أضيفي للمفضلة',
		'Remove from wishlist'         => 'احذفي من المفضلة',
		'Add to Wishlist'              => 'أضيفي للمفضلة',
		'Saved to Wishlist'            => 'اتحفظت في المفضلة',
		'%s item'                      => 'منتج واحد',
		'%s items'                     => '%s منتجات',

		// ── Shop ─────────────────────────────────────────────────────
		'Discover the Lily collection of colored and clear lenses, designed for a natural and effortless look.' => 'اكتشفي مجموعة ليلي من العدسات الملونة والشفافة، المصممة لإطلالة طبيعية بدون مجهود.',
		'Brand'                        => 'الماركة',
		'Color'                        => 'اللون',
		'Featured'                     => 'المميز',
		'Newest'                       => 'الأحدث',
		'Price: Low to High'           => 'السعر: من الأقل للأعلى',
		'Price: High to Low'           => 'السعر: من الأعلى للأقل',
		'Name: A–Z'                    => 'الاسم: أ – ي',
		'All'                          => 'الكل',
		'All Products'                 => 'كل المنتجات',
		'Collections'                  => 'المجموعات',
		'Filter By'                    => 'تصفية حسب',
		'Filter'                       => 'تصفية',
		'Sort by'                      => 'ترتيب حسب',
		'Go'                           => 'عرض',
		'Show more'                    => 'عرض المزيد',
		'Show less'                    => 'عرض أقل',
		'Clear %s'                     => 'مسح %s',
		'Clear all'                    => 'مسح الكل',
		'Price'                        => 'السعر',
		'Min'                          => 'من',
		'Max'                          => 'إلى',
		'Apply'                        => 'تطبيق',
		'Minimum price'                => 'أقل سعر',
		'Maximum price'                => 'أعلى سعر',
		'No products were found matching your selection.' => 'لا توجد منتجات مطابقة لاختيارك.',
		'Products coming soon.'        => 'المنتجات قريباً.',
		'%s Product'                   => 'منتج واحد',
		'%s Products'                  => '%s منتجات',
		'Shop categories'              => 'أقسام التسوق',
		'Filter products'              => 'تصفية المنتجات',
		'Close filters'                => 'إغلاق التصفية',
		'Clear %s'                     => 'مسح %s',
		'Contact form'                 => 'نموذج التواصل',

		// ── Single product ───────────────────────────────────────────
		'Previous image'               => 'الصورة السابقة',
		'Next image'                   => 'الصورة التالية',
		'Increase quantity'            => 'زيادة الكمية',
		'Decrease quantity'            => 'تقليل الكمية',
		'Duration'                     => 'المدة',
		'Diameter'                     => 'القطر',
		'Water Content'                => 'نسبة الماء',
		'Base Curve'                   => 'انحناء العدسة',
		'Lens Information'             => 'معلومات العدسة',
		'Lens Details'                 => 'تفاصيل العدسة',
		'Perfect Match'                => 'التناسق المثالي',
		'Best Suited For'              => 'الأنسب لـ',
		'Original Eye Color'           => 'لون العين الأصلي',
		'Perfect For'                  => 'مثالية لـ',
		'Best suited for'              => 'الأنسب لـ',
		'Look'                         => 'اللوك',
		'Please make sure you select the correct color before confirming your order.' => 'من فضلك تأكدي إنك مختارة اللون الصح قبل تأكيد طلبك.',
		'Available'                    => 'متاح',
		'Customer Service & Complaints' => 'خدمة العملاء والشكاوى',
		'Shipping & Exchange'          => 'الشحن والاستبدال',
		'Easy and hassle-free'         => 'سهل ومن غير تعقيد',
		'Description'                  => 'الوصف',
		'Details'                      => 'التفاصيل',
		'How to use'                   => 'طريقة الاستخدام',
		'Wash and dry your hands before handling your lenses. Place the lens on your fingertip, check it forms a smooth cup shape, then gently place it on your eye. Remove lenses before sleeping or swimming, clean them with sterile contact-lens solution, and replace them on schedule.' => 'اغسلي إيدك ونشفيها قبل ما تمسكي العدسات. حطي العدسة على طرف صباعك، وتأكدي إنها واخدة شكل كوب منتظم، وبعدين حطيها على عينك بلطف. اخلعي العدسات قبل النوم أو السباحة، ونضفيها بمحلول معقم، واستبدليها في ميعادها.',
		'Reviews (%d)'                 => 'التقييمات (%d)',
		'You May Also Like'            => 'ممكن يعجبك أيضاً',
		'View All'                     => 'عرض الكل',

		// ── Reviews ──────────────────────────────────────────────────
		'Reviews'                      => 'التقييمات',
		'What our customers are saying' => 'آراء عميلاتنا',
		'Write a review'               => 'اكتبي تقييمك',
		'Write a Review'               => 'اكتبي تقييمك',
		'Submit review'                => 'إرسال التقييم',
		'Submit Review'                => 'إرسال التقييم',
		'Thank you! Your review is awaiting moderation and will appear once approved.' => 'شكراً لكِ! تقييمك في انتظار المراجعة وهيظهر بعد الموافقة عليه.',
		'Based on %d review'           => 'بناءً على تقييم واحد',
		'Based on %d reviews'          => 'بناءً على %d تقييمات',
		'Verified Purchase'            => 'عملية شراء موثقة',
		'No reviews yet'               => 'لا توجد تقييمات بعد',
		'Be the first to share your experience with this lens.' => 'كوني أول واحدة تشارك تجربتها مع هذه العدسة.',
		'Share your experience'        => 'شاركينا تجربتك',
		'Your rating'                  => 'تقييمك',
		'%d star'                      => 'نجمة واحدة',
		'%d stars'                     => '%d نجوم',
		'Your review'                  => 'تقييمك',
		'Posting as %s.'               => 'بتنشري باسم %s.',
		'Name'                         => 'الاسم',
		'Photos (optional)'            => 'صور (اختياري)',
		'Up to 3 images — JPG, PNG or WebP, max 5 MB each.' => 'حتى ٣ صور — JPG أو PNG أو WebP، بحد أقصى ٥ ميجا للصورة.',
		'Reviews are moderated and appear once approved.' => 'التقييمات بتتراجع وبتظهر بعد الموافقة عليها.',
		'Review photo'                 => 'صورة التقييم',
		'Close photo'                  => 'إغلاق الصورة',
		'Review photo enlarged'        => 'صورة التقييم مكبرة',
		'Open review photo %d'         => 'فتح صورة التقييم %d',
		'Customer review photo %d'     => 'صورة تقييم العميلة %d',
		'Anonymous'                    => 'بدون اسم',
		'Rated %s out of 5'            => 'التقييم %s من ٥',

		// ── Cart / drawer ────────────────────────────────────────────
		'Your cart is empty.'          => 'سلتك فاضية.',
		'RETURN TO SHOP'               => 'العودة للتسوق',
		'Return to Shop'               => 'العودة للتسوق',
		'Return to shop'               => 'العودة للتسوق',
		'Discover our lenses and find the shade that suits you best.' => 'اكتشفي عدساتنا واعثري على الدرجة الأنسب لكِ.',
		'Subtotal'                     => 'المجموع الفرعي',
		'Shipping'                     => 'الشحن',
		'Calculated at checkout'       => 'يُحسب أثناء إتمام الطلب',
		'CHECKOUT'                     => 'إتمام الطلب',
		'Your Cart'                    => 'سلة التسوق',
		'Close cart'                   => 'إغلاق السلة',
		'Shopping cart'                => 'سلة التسوق',
		'Remove %s from cart'          => 'احذفي %s من السلة',
		'Sorry, this product could not be added.' => 'عذراً، تعذر إضافة هذا المنتج.',
		'Order Summary'                => 'ملخص الطلب',
		'Discount'                     => 'الخصم',
		'Discount applied'             => 'تم تطبيق الخصم',
		'Remove'                       => 'احذفي',
		'Discount code'                => 'كود الخصم',
		'Enter your code'              => 'ادخلي الكود',
		'Update cart'                  => 'تحديث السلة',
		'Proceed to Checkout'          => 'إتمام الطلب',
		'Continue Shopping'            => 'مواصلة التسوق',
		'Total'                        => 'الإجمالي',

		// ── Checkout ─────────────────────────────────────────────────
		'Email (optional)'             => 'البريد الإلكتروني (اختياري)',
		'Governorate / Area'           => 'المحافظة / المنطقة',
		'Detailed Address'             => 'العنوان بالتفصيل',
		'Area, street, building, floor, apartment, landmark…' => 'المنطقة، الشارع، العمارة، الدور، الشقة، علامة مميزة...',
		'Delivery Note'                => 'ملاحظة التوصيل',
		'Any delivery instructions for the courier' => 'أي تعليمات للتوصيل للمندوب',
		'Select your area'             => 'اختاري منطقتك',
		'Please select your Governorate / Area for delivery.' => 'من فضلك اختاري المحافظة / المنطقة للتوصيل.',
		'Please enter your phone number.' => 'من فضلك ادخلي رقم تليفونك.',
		'Please enter your detailed address.' => 'من فضلك ادخلي عنوانك بالتفصيل.',
		'Cairo'                        => 'القاهرة',
		'Giza'                         => 'الجيزة',
		'New Cities'                   => 'المدن الجديدة',
		'Giza Suburbs'                 => 'ضواحي الجيزة',
		'Alexandria'                   => 'الإسكندرية',
		'Qalyubia'                     => 'القليوبية',
		'Dakahlia'                     => 'الدقهلية',
		'Sharqia'                      => 'الشرقية',
		'Gharbia'                      => 'الغربية',
		'Monufia'                      => 'المنوفية',
		'Beheira'                      => 'البحيرة',
		'Kafr El Sheikh'               => 'كفر الشيخ',
		'Damietta'                     => 'دمياط',
		'New Damietta'                 => 'دمياط الجديدة',
		'Port Said'                    => 'بورسعيد',
		'Ismailia'                     => 'الإسماعيلية',
		'Suez'                         => 'السويس',
		'Fayoum'                       => 'الفيوم',
		'Beni Suef'                    => 'بني سويف',
		'Minya'                        => 'المنيا',
		'Assiut'                       => 'أسيوط',
		'Sohag'                        => 'سوهاج',
		'Qena'                         => 'قنا',
		'Luxor'                        => 'الأقصر',
		'Aswan'                        => 'أسوان',
		'New Valley'                   => 'الوادي الجديد',
		'Marsa Matrouh'                => 'مرسى مطروح',
		'North Sinai'                  => 'شمال سيناء',
		'South Sinai'                  => 'جنوب سيناء',
		'Sharm El Sheikh'              => 'شرم الشيخ',
		'Red Sea'                      => 'البحر الأحمر',
		'Hurghada'                     => 'الغردقة',
		'North Coast'                  => 'الساحل الشمالي',
		'New Cairo, New Capital, El Shorouk, El Obour, Madinaty, El Rehab' => 'التجمع، العاصمة الجديدة، الشروق، العبور، مدينتي، الرحاب',
		'6th of October, Sheikh Zayed, Hadayek October, October Gardens' => '٦ أكتوبر، الشيخ زايد، حدائق أكتوبر',

		// ── Footer ───────────────────────────────────────────────────
		'Since 2014'                   => 'منذ ٢٠١٤',
		'منذ 2014'                     => 'منذ ٢٠١٤',
		'Since 2014, Lily has been bringing carefully selected lenses and everyday eye-care essentials together with a simple, thoughtful approach to beauty and style.' => 'منذ ٢٠١٤، بتقدم ليلي عدسات مختارة بعناية ومستلزمات العناية اليومية بالعين، برؤية بسيطة ومدروسة للجمال والأناقة.',
		'Follow Us'                    => 'تابعينا',
		'Facebook'                     => 'فيسبوك',
		'Facebook Group'               => 'جروب فيسبوك',
		'TikTok'                       => 'تيك توك',
		'YouTube'                      => 'يوتيوب',
		'Privacy Policy'               => 'سياسة الخصوصية',
		'Privacy policy details'       => 'تفاصيل سياسة الخصوصية',
		'Copyright %1$s %2$s. All rights reserved.' => 'حقوق النشر %1$s %2$s. جميع الحقوق محفوظة.',
		'Cash on Delivery Available'   => 'الدفع عند الاستلام متاح',

		// ── Prescription (product page / cart / order) ─────────────────
		'Prescription Power'           => 'مقاس النظر',
		'Right Eye (OD)'               => 'العين اليمين (OD)',
		'Left Eye (OS)'                => 'العين الشمال (OS)',
		'Select power'                 => 'اختاري المقاس',
		'Choose 0.00 for an eye that does not need power.' => 'اختاري 0.00 للعين اللي مش محتاجة مقاس.',
		'Quantity'                     => 'الكمية',
		'Please select the prescription power for both eyes.' => 'من فضلك اختاري مقاس النظر للعينين.',
		'Right Eye (OD): %1$s · Left Eye (OS): %2$s' => 'العين اليمين (OD): %1$s · العين الشمال (OS): %2$s',
		'Right Eye: %1$s · Left Eye: %2$s' => 'العين اليمين: %1$s · العين الشمال: %2$s',
		'%1$s requires prescription power selection for both eyes.' => '%1$s محتاجة اختيار مقاس النظر للعينين.',
		'Yes'                          => 'نعم',
		'No'                           => 'لا',
		'Email'                        => 'البريد الإلكتروني',
		'Phone'                        => 'التليفون',
		'Help'                         => 'مساعدة',
		'About'                        => 'عن ليلي',
		'Accessibility'                => 'سهولة الوصول',
		'Lens Finder'                  => 'دليل اختيار العدسات',
		'Shipping & Delivery'          => 'الشحن والتوصيل',
		'Returns & Exchanges'          => 'الاسترجاع والاستبدال',
		'FAQ'                          => 'الأسئلة الشائعة',
		'Safe & Secure'                => 'آمن ومضمون',
		'High quality lenses with exceptional comfort.' => 'عدسات عالية الجودة براحة استثنائية.',
		'Your data is safe with us. Always.' => 'بياناتك في أمان معنا. دايماً.',
		'Quick and reliable shipping to your doorstep.' => 'شحن سريع وموثوق لحد باب البيت.',
		'Stay in the know'             => 'خليكِ على اطلاع',
		'Join our community and get 10% off your first order.' => 'انضمي لمجتمعنا واحصلي على خصم ١٠٪ على أول طلب.',
		'Your email address'           => 'بريدك الإلكتروني',
		'By subscribing, you agree to our Privacy Policy and consent to receive updates from Lily.' => 'بالاشتراك، أنتِ توافقين على سياسة الخصوصية وتوافقين على استلام تحديثات من ليلي.',
		'Timeless lenses.'             => 'عدسات خالدة.',
		'Designed for you.'            => 'مصممة لكِ.',
		'Made to be seen.'             => 'اتعملت عشان تبان.',
	);

	return $map;
}

/**
 * Look up one English string in the Arabic dictionary.
 *
 * @param string $english English source string.
 * @return string|null Arabic translation or null when unknown.
 */
function lily_ar_lookup( $english ) {
	if ( ! is_string( $english ) || '' === $english ) {
		return null;
	}

	$map = lily_ar_dictionary();

	return array_key_exists( $english, $map ) ? $map[ $english ] : null;
}

/**
 * Dashboard fallback: Arabic translation of a saved English value.
 *
 * Used when the brand owner has not entered Arabic yet. Returns the
 * dictionary translation when the whole English value is known, otherwise
 * returns the English value itself (safe fallback — never empty output).
 *
 * @param string $english Saved English value.
 * @return string
 */
function lily_ar_fallback( $english ) {
	$found = lily_ar_lookup( (string) $english );

	return null !== $found ? $found : (string) $english;
}

/**
 * Look up the TranslatePress-owned Arabic translation of an English string.
 *
 * Reads the SAME wp_trp_dictionary_* tables the TranslatePress editor
 * writes (human-reviewed rows only). Used only for display contexts the
 * TranslatePress HTML parser cannot reach on its own: text glued to HTML
 * entities (breadcrumb separators) and the <title> tag in <head>.
 * TranslatePress remains the single writer/owner of these translations;
 * this is a read-only compatibility shim, not a second engine.
 *
 * @param string $english English source string.
 * @return string|null Arabic translation or null when unknown.
 */
function lily_trp_lookup( $english ) {
	if ( ! is_string( $english ) || '' === trim( $english ) ) {
		return null;
	}

	static $cache = array();

	if ( array_key_exists( $english, $cache ) ) {
		return $cache[ $english ];
	}

	$found = null;

	if ( isset( $GLOBALS['wpdb'] ) && is_object( $GLOBALS['wpdb'] ) ) {
		global $wpdb;

		$table = $wpdb->prefix . 'trp_dictionary_en_us_ar';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery -- read-only mirror of TranslatePress storage.
		$row = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT translated FROM `$table` WHERE original = %s AND status = 2 AND translated <> '' LIMIT 1",
				$english
			),
			ARRAY_A
		);

		if ( $row && isset( $row['translated'] ) && '' !== trim( (string) $row['translated'] ) ) {
			$found = (string) $row['translated'];
		}
	}

	if ( null === $found ) {
		$found = lily_ar_lookup( $english );
	}

	$cache[ $english ] = $found;

	return $found;
}

/**
 * Translate one display string through TranslatePress storage.
 *
 * Tries the exact string, then its entity-decoded and entity-encoded
 * forms (TranslatePress keys text nodes both ways depending on markup).
 * Returns the English input unchanged when nothing is known.
 *
 * @param string $text English text.
 * @return string
 */
function lily_trp_display( $text ) {
	if ( ! is_string( $text ) || '' === $text ) {
		return (string) $text;
	}

	$candidates = array( $text );

	$decoded = html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );
	if ( $decoded !== $text ) {
		$candidates[] = $decoded;
	}

	$encoded = htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
	if ( $encoded !== $text ) {
		$candidates[] = $encoded;
	}

	foreach ( $candidates as $candidate ) {
		$found = lily_trp_lookup( $candidate );

		if ( null !== $found ) {
			return $found;
		}
	}

	return $text;
}

/**
 * Translate WooCommerce breadcrumb crumbs on Arabic requests.
 *
 * The current-page crumb is plain text glued to &nbsp;/&#47; separators,
 * so the TranslatePress HTML parser never matches it. URLs and structure
 * are untouched — only the visible label is swapped when TranslatePress
 * owns a translation for it.
 *
 * @param array $crumbs Breadcrumb pairs of [ text, url ].
 * @return array
 */
function lily_ar_breadcrumb( $crumbs ) {
	if ( ! lily_should_serve_arabic() || ! is_array( $crumbs ) ) {
		return $crumbs;
	}

	foreach ( $crumbs as $i => $crumb ) {
		if ( ! is_array( $crumb ) || ! isset( $crumb[0] ) || ! is_string( $crumb[0] ) || '' === trim( wp_strip_all_tags( $crumb[0] ) ) ) {
			continue;
		}

		$crumbs[ $i ][0] = lily_trp_display( $crumb[0] );
	}

	return $crumbs;
}
add_filter( 'woocommerce_get_breadcrumb', 'lily_ar_breadcrumb', 20 );

/**
 * Translate the <title> tag on Arabic requests.
 *
 * The TranslatePress HTML parser does not rewrite <head> content and its
 * gettext path explicitly skips wp_get_document_title(), so the browser
 * tab would otherwise stay English. Only the title text is swapped (via
 * TranslatePress storage first, Lily dictionary second); site name,
 * pagination and structure are untouched.
 *
 * @param array $parts Document title parts.
 * @return array
 */
function lily_ar_document_title( $parts ) {
	if ( ! lily_should_serve_arabic() || ! is_array( $parts ) || empty( $parts['title'] ) ) {
		return $parts;
	}

	$title = (string) $parts['title'];
	$found = lily_trp_display( $title );

	if ( $found === $title ) {
		// Search pages carry the dynamic query: "Search Results for "x"".
		foreach ( array( 'Search Results for ', 'Search results for: ' ) as $prefix ) {
			if ( 0 === strpos( $title, $prefix ) ) {
				$found = lily_trp_display( rtrim( $prefix ) ) . ' ' . substr( $title, strlen( $prefix ) );
				break;
			}
		}
	}

	$parts['title'] = $found;

	return $parts;
}
add_filter( 'document_title_parts', 'lily_ar_document_title', 20 );

/**
 * Whether Arabic output should be served for the current request.
 *
 * @return bool
 */
function lily_should_serve_arabic() {
	if ( function_exists( 'lily_is_arabic_request' ) && lily_is_arabic_request() ) {
		return true;
	}

	if ( function_exists( 'is_admin' ) && is_admin() ) {
		return false;
	}

	if ( function_exists( 'get_locale' ) ) {
		$base = strtolower( strtok( (string) get_locale(), '_-' ) );

		if ( 'ar' === $base ) {
			return true;
		}
	}

	return false;
}

/**
 * Translate a "lily" textdomain string to Arabic on Arabic requests.
 *
 * @param string $translated Already-translated text.
 * @param string $text       Original English text.
 * @param string $domain     Textdomain.
 * @return string
 */
function lily_ar_gettext( $translated, $text, $domain ) {
	if ( 'lily' !== $domain || ! lily_should_serve_arabic() ) {
		return $translated;
	}

	$found = lily_ar_lookup( (string) $text );

	return null !== $found ? $found : $translated;
}

/**
 * Translate plural "lily" strings on Arabic requests.
 *
 * @param string $translated Already-translated text.
 * @param string $single     Singular form.
 * @param string $plural     Plural form.
 * @param int    $number     Count.
 * @param string $domain     Textdomain.
 * @return string
 */
function lily_ar_ngettext( $translated, $single, $plural, $number, $domain ) {
	if ( 'lily' !== $domain || ! lily_should_serve_arabic() ) {
		return $translated;
	}

	$key   = ( 1 === (int) $number ) ? (string) $single : (string) $plural;
	$found = lily_ar_lookup( $key );

	return null !== $found ? $found : $translated;
}

add_filter( 'gettext', 'lily_ar_gettext', 20, 3 );

/**
 * Translate contextual "lily" strings on Arabic requests.
 *
 * @param string $translated Already-translated text.
 * @param string $text       Original text.
 * @param string $context    Context.
 * @param string $domain     Textdomain.
 * @return string
 */
function lily_ar_gettext_with_context( $translated, $text, $context, $domain ) {
	return lily_ar_gettext( $translated, $text, $domain );
}

add_filter( 'gettext_with_context', 'lily_ar_gettext_with_context', 20, 4 );
add_filter( 'ngettext', 'lily_ar_ngettext', 20, 5 );
add_filter( 'ngettext_with_context', 'lily_ar_ngettext_with_context', 20, 6 );

/**
 * Translate plural+context "lily" strings on Arabic requests.
 *
 * @param string $translated Already-translated text.
 * @param string $single     Singular.
 * @param string $plural     Plural.
 * @param int    $number     Count.
 * @param string $context    Context.
 * @param string $domain     Textdomain.
 * @return string
 */
function lily_ar_ngettext_with_context( $translated, $single, $plural, $number, $context, $domain ) {
	return lily_ar_ngettext( $translated, $single, $plural, $number, $domain );
}
