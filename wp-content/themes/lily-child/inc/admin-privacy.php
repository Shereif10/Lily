<?php
/**
 * Lily Privacy Policy dashboard fields (slug: privacy-policy).
 *
 * Reuses the existing Lily settings system (same option, same save flow,
 * same admin helpers as the other legal pages): the privacy_* keys live
 * inside `lily_homepage_settings` and render as a "Privacy Page" tab on
 * the existing Lily admin page. The page template reuses the Terms &
 * Conditions markup and CSS classes, so no new design system is created.
 *
 * Single source of truth: lily_privacy_approved_content() holds the
 * complete approved English + Arabic policy verbatim. The template uses it
 * as fallback and the initial DB population copies it into the dashboard
 * fields, so the brand owner can edit both languages without code.
 *
 * Note: this page has no eyebrow element by design (title appears once).
 * Placeholder items from the source review (company name, address, email,
 * data-protection contact) are intentionally omitted everywhere.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Complete approved Privacy Policy content (verbatim source).
 *
 * @return array
 */
function lily_privacy_approved_content() {
	static $content = null;

	if ( null !== $content ) {
		return $content;
	}

	$content = array(
		'hero_title_en' => 'Privacy Policy',
		'hero_title_ar' => 'سياسة الخصوصية',
		'hero_intro_en' => 'Learn how Lily Original Lenses collects, uses, protects and handles your personal information when you use our website, place an order or communicate with us.

At Lily – Lily Original Lenses, we respect your privacy and treat your personal data as a matter of trust and responsibility. This policy explains the types of information we may collect, the purposes for which we use it, how we protect it, the situations in which we may share it, and the rights available to you in relation to your personal data.

This policy applies to your use of lilylenses.com and to orders and communications made through the website or through communication channels connected to our service.

Where the law requires your consent for a particular type of processing of your personal data, we will request that consent separately, clearly and explicitly.',
		'hero_intro_ar' => 'تعرّفي على كيفية جمع ليلي لبياناتك الشخصية واستخدامها وحمايتها والتعامل معها عند استخدامك للموقع أو تقديم طلب أو التواصل معنا.

في ليلي – Lily Original Lenses، نحترم خصوصيتك ونتعامل مع بياناتك الشخصية باعتبارها أمانة ومسؤولية. توضح هذه السياسة أنواع البيانات التي قد نجمعها، والأغراض التي نستخدمها من أجلها، وكيف نحميها، والحالات التي قد نشاركها فيها، والحقوق المتاحة لك فيما يتعلق ببياناتك.

تنطبق هذه السياسة على استخدامك لموقع lilylenses.com وعلى الطلبات والتواصل الذي يتم من خلال الموقع أو وسائل التواصل المرتبطة بخدمتنا.

في الحالات التي يتطلب فيها القانون الحصول على موافقتك على معالجة معينة لبياناتك، سنطلب هذه الموافقة منك بصورة منفصلة وواضحة وصريحة.',
		'sections'      => array(
			array(
				'num'      => '01',
				'title_en' => 'WHO IS RESPONSIBLE FOR YOUR DATA?',
				'body_en'  => 'The party responsible for collecting and processing your personal data is:

Trade Name:

Lily Original Lenses – Lily

Website:

lilylenses.com

Customer Service & WhatsApp:

01060760098',
				'title_ar' => 'من المسؤول عن بياناتك؟',
				'body_ar'  => 'المسؤول عن جمع ومعالجة بياناتك الشخصية هو:

الاسم التجاري:

Lily Original Lenses – ليلي

الموقع الإلكتروني:

lilylenses.com

خدمة العملاء وواتساب:

01060760098',
			),
			array(
				'num'      => '02',
				'title_en' => 'THE DATA WE COLLECT',
				'body_en'  => 'Depending on how you use the website and the nature of your order, we may collect the following types of information.

ORDER AND DELIVERY DATA

This may include:

- Your full name.
- Your detailed delivery address.
- Your primary mobile phone number.
- An alternative phone number if you choose to provide one.
- Whether your contact number is available on WhatsApp, if you choose to communicate with us through that method.
- Order details and requested products.
- Any necessary delivery instructions.

We request only the information necessary to process, deliver and communicate with you regarding your order.

PRODUCT DATA

This may include:

- Contact lens color.
- Usage duration.
- Size.
- Prescription power or optical specification where required for the product you order.
- Quantity and any other product-related selections.

COMMUNICATION DATA

If you contact us through WhatsApp, telephone or another available support channel, we may retain the information necessary in connection with that communication, such as:

- Order-related messages.
- Enquiries.
- Complaints.
- Exchange requests or reports relating to manufacturing defects.
- Any information you voluntarily provide to help resolve an issue or complete your request.

TECHNICAL DATA

When you browse the website, certain technical information may be collected automatically, such as:

- Device type.
- Operating system.
- Browser type.
- Internet Protocol (IP) address or similar electronic identifiers, depending on the technologies used on the website.
- Pages you visit.
- Duration of your visit.
- How you interact with the website.
- General information about the source of your visit.

We use this information to operate and secure the website, understand its performance and improve the user experience, according to the technical tools actually enabled on the website.',
				'title_ar' => 'البيانات التي نجمعها',
				'body_ar'  => 'بحسب طريقة استخدامك للموقع وطبيعة طلبك، قد نجمع الأنواع التالية من البيانات.

بيانات الطلب والتوصيل

قد تشمل:

- الاسم بالكامل.
- عنوان التوصيل بالتفصيل.
- رقم هاتف محمول أساسي.
- رقم هاتف بديل إذا اخترت تقديمه.
- ما إذا كان رقم التواصل متاحًا عبر واتساب، إذا اخترت التواصل بهذه الطريقة.
- تفاصيل الطلب والمنتجات المطلوبة.
- أي تعليمات ضرورية متعلقة بالتوصيل.

نطلب فقط البيانات اللازمة لتنفيذ الطلب وتوصيله والتواصل بشأنه.

بيانات المنتج

قد تشمل:

- لون العدسات.
- مدة الاستخدام.
- المقاس.
- درجة النظر أو الدرجة الطبية إذا كان المنتج الذي تطلبه يتطلب ذلك.
- الكمية وأي اختيارات أخرى مرتبطة بالمنتج.

بيانات التواصل

إذا تواصلت معنا عبر واتساب أو الهاتف أو أي وسيلة دعم متاحة، فقد نحتفظ بالبيانات اللازمة المتعلقة بهذا التواصل، مثل:

- محتوى الرسائل المتعلقة بالطلب.
- الاستفسارات.
- الشكاوى.
- طلبات الاستبدال أو معالجة عيوب الصناعة.
- أي معلومات تقدمها إلينا بصورة اختيارية لحل المشكلة أو تنفيذ طلبك.

البيانات التقنية

عند تصفح الموقع، قد تجمع بعض المعلومات التقنية بصورة تلقائية، مثل:

- نوع الجهاز.
- نظام التشغيل.
- نوع المتصفح.
- عنوان بروتوكول الإنترنت (IP) أو معرّفات إلكترونية مشابهة، بحسب التقنيات المستخدمة في الموقع.
- الصفحات التي تزورها.
- مدة الزيارة.
- طريقة التفاعل مع الموقع.
- مصدر الزيارة بصورة عامة.

نستخدم هذه البيانات لتشغيل الموقع وتأمينه وفهم أدائه وتحسين تجربة الاستخدام، وفقًا للأدوات التقنية المفعّلة فعليًا على الموقع.',
			),
			array(
				'num'      => '03',
				'title_en' => 'PRESCRIPTION AND EYE-RELATED DATA',
				'body_en'  => 'Some of our products may be ordered with a prescription power or medical specification selected by the customer. Because this information may be considered health-related data or sensitive personal data, we handle it with additional care.

Please note the following:

- Choosing the appropriate prescription power or size is the customer\'s responsibility.
- Lily Original Lenses does not perform eye examinations.
- We do not diagnose medical conditions.
- We do not provide medical advice regarding the appropriate prescription for the customer.
- We do not verify the accuracy of the prescription power entered by the customer.
- No content published on the website replaces consultation with an ophthalmologist or another qualified professional.

If you enter prescription information as part of your order, we use it only to the extent necessary to prepare the requested product.

Where necessary, we will ask for your explicit consent to process prescription information for the purpose of fulfilling your order.

We do not use prescription information for advertising, marketing or building marketing profiles about customers.

Access to this information within Lily is limited to persons whose duties require access in order to fulfill the order or provide customer service, with appropriate measures taken to protect its confidentiality.

We do not share this information with other parties unless this is legally and legitimately necessary, or unless we are required to do so under a legal obligation or an order issued by a competent authority.',
				'title_ar' => 'بيانات درجة النظر والبيانات المتعلقة بصحة العين',
				'body_ar'  => 'بعض منتجاتنا يمكن طلبها بدرجة نظر أو مواصفات طبية يحددها العميل، ونظرًا لأن هذه البيانات قد تعد من البيانات المتعلقة بالحالة الصحية أو البيانات الشخصية الحساسة، فإننا نتعامل معها بعناية إضافية.

يرجى ملاحظة ما يلي:

- اختيار درجة النظر أو المقاس المناسب هو مسؤولية العميل.
- موقع Lily Original Lenses لا يُجري فحصًا للنظر.
- نحن لا نشخص أي حالة طبية.
- لا نقدم توصية طبية بشأن الدرجة المناسبة للعميل.
- لا نتحقق من صحة الدرجة التي يدخلها العميل.
- لا يغني أي محتوى منشور على الموقع عن استشارة طبيب عيون أو مختص مؤهل.

إذا أدخلت درجة النظر ضمن طلبك، فإننا نستخدمها فقط بالقدر اللازم لتجهيز المنتج المطلوب.

وعند الحاجة سيطلب منك تقديم موافقة صريحة على معالجة بيانات درجة النظر لغرض تنفيذ طلبك.

لا نستخدم بيانات درجة النظر في الإعلانات أو التسويق أو بناء ملفات تسويقية عن العملاء.

ويقتصر الوصول إليها داخل ليلي على الأشخاص الذين تتطلب مهامهم الاطلاع عليها من أجل تنفيذ الطلب أو خدمة العميل، مع اتخاذ التدابير المناسبة لحماية سريتها.

ولا نشارك هذه البيانات مع أطراف أخرى إلا إذا كان ذلك ضروريًا بصورة قانونية ومشروعة، أو مطلوبًا منا بموجب التزام قانوني أو أمر صادر من جهة مختصة.',
			),
			array(
				'num'      => '04',
				'title_en' => 'PAYMENT DATA',
				'body_en'  => 'All payment methods currently available are made upon delivery or through payment methods announced to the customer, which may include:

- Cash on delivery.
- InstaPay.
- Electronic wallets.

The website does not currently collect credit card or debit card information, and we do not ask you to enter your online banking login details.

When payment is made using InstaPay or an electronic wallet, we may receive limited information relating to confirmation of the transfer, such as the sender\'s name, a transaction reference number or proof of payment if the customer chooses to send it to us.

This information is used only to verify payment and process the order.

We will never ask you to send us your password, confidential verification code or any information that would allow us to access your financial account.',
				'title_ar' => 'بيانات الدفع',
				'body_ar'  => 'جميع المدفوعات المتاحة لدينا تتم عند الاستلام أو وفق وسائل الدفع التي نعلن عنها للعميل، والتي قد تشمل:

- الدفع النقدي عند الاستلام.
- إنستا باي.
- المحافظ الإلكترونية.

لا يستقبل الموقع في الوقت الحالي بيانات بطاقات ائتمانية أو بطاقات خصم مباشر، ولا نطلب منك إدخال بيانات تسجيل الدخول إلى حسابك البنكي.

وعند الدفع باستخدام إنستا باي أو محفظة إلكترونية، قد نتلقى بيانات محدودة مرتبطة بتأكيد عملية التحويل، مثل اسم المرسل أو رقم مرجعي للعملية أو صورة لإثبات التحويل إذا قام العميل بإرسالها إلينا.

تستخدم هذه البيانات فقط للتحقق من الدفع ومعالجة الطلب، ولا نطلب منك إرسال كلمة مرور أو رمز تحقق سري أو أي بيانات تسمح لنا بالدخول إلى حسابك المالي.',
			),
			array(
				'num'      => '05',
				'title_en' => 'WHY DO WE USE YOUR DATA?',
				'body_en'  => 'We may use personal data for the following purposes:

- Creating and preparing your order.
- Providing the product according to the specifications you selected.
- Delivering your order to the correct address.
- Contacting you to confirm your order.
- Contacting you regarding order or delivery status.
- Responding to enquiries and complaints.
- Handling exchanges or manufacturing defects.
- Verifying payments made through InstaPay or electronic wallets.
- Maintaining commercial and accounting records required by law.
- Preventing misuse of the website or attempted fraud.
- Securing the website and diagnosing technical issues.
- Improving website performance and user experience.
- Sending marketing messages, but only where we have obtained any consent required for that purpose.',
				'title_ar' => 'لماذا نستخدم بياناتك؟',
				'body_ar'  => 'قد نستخدم البيانات الشخصية للأغراض التالية:

- إنشاء الطلب وتجهيزه.
- توفير المنتج بالمواصفات التي اخترتها.
- توصيل الطلب إلى العنوان الصحيح.
- التواصل معك لتأكيد الطلب.
- التواصل معك بشأن حالة الطلب أو التوصيل.
- الرد على الاستفسارات والشكاوى.
- التعامل مع حالات الاستبدال أو عيوب الصناعة.
- التحقق من عمليات الدفع عند استخدام إنستا باي أو المحافظ الإلكترونية.
- الاحتفاظ بالسجلات التجارية والمحاسبية التي يفرضها القانون.
- منع إساءة استخدام الموقع أو محاولات الاحتيال.
- تأمين الموقع وتشخيص المشكلات التقنية.
- تحسين أداء الموقع وتجربة المستخدم.
- إرسال رسائل تسويقية، ولكن فقط في الحالات التي حصلنا فيها على الموافقة المطلوبة لذلك.',
			),
			array(
				'num'      => '06',
				'title_en' => 'THE LEGAL BASIS FOR PROCESSING YOUR DATA',
				'body_en'  => 'We process your personal data only where we have a lawful basis that allows us to do so. The legal basis may vary depending on the purpose of the processing.

These situations may include:

- Fulfilling your order or taking steps at your request, such as collecting your name, address and phone number and preparing and delivering your order.
- Complying with a legal obligation, such as retaining certain commercial or accounting records where required by law.
- Consent, where the law requires your consent, such as for certain marketing activities or the processing of certain sensitive data.
- Protecting legal rights, where processing is necessary to establish, exercise or defend a legal claim.
- Legitimate interests, where permitted by law, such as securing the website, preventing fraud and improving the service, provided that these interests do not override your fundamental rights and freedoms.',
				'title_ar' => 'الأساس الذي نعتمد عليه في معالجة البيانات',
				'body_ar'  => 'نعالج بياناتك فقط عندما يكون لدينا أساس مشروع يسمح بذلك، وقد يختلف الأساس بحسب الغرض من المعالجة.

ومن بين هذه الحالات:

- تنفيذ طلبك أو اتخاذ إجراءات بناءً على طلبك: مثل جمع الاسم والعنوان ورقم الهاتف وتجهيز المنتج وتوصيله.
- الوفاء بالتزام قانوني: مثل الاحتفاظ ببعض السجلات التجارية أو المحاسبية عندما يلزمنا القانون بذلك.
- الموافقة: في الحالات التي يتطلب فيها القانون موافقة منك، مثل بعض عمليات التسويق أو معالجة بعض البيانات الحساسة.
- حماية الحقوق القانونية: إذا كانت المعالجة ضرورية لإثبات حق أو المطالبة به أو الدفاع عنه.
- المصلحة المشروعة: في الحالات التي يسمح بها القانون، مثل تأمين الموقع ومنع الاحتيال وتحسين الخدمة، بشرط ألا تتغلب هذه المصلحة على حقوقك وحرياتك الأساسية.',
			),
			array(
				'num'      => '07',
				'title_en' => 'SHARING DATA WITH OTHERS',
				'body_en'  => 'Customer deliveries are currently handled through a delivery team directly affiliated with us. Therefore, we do not provide your order information to an external shipping company solely for the purpose of completing delivery, as long as this delivery system remains in place.

However, we may use third-party technical services that are necessary to operate our business or website, such as:

- Website hosting and server services.
- Backup services.
- Security and technical protection services.
- Website operation tools.
- Analytics and performance measurement services, where enabled.
- Communication services chosen by the customer, such as WhatsApp.
- Other technical service providers where necessary to operate the service.

In such cases, we seek to provide these parties only with the information necessary to perform the required service and to ensure that personal data is processed within legally permitted limits and with appropriate safeguards.

We do not sell your personal data, rent it or exchange customer lists with other parties for their own marketing purposes.

We may also be required to disclose certain information where disclosure is required by law or in order to comply with a court judgment or an official order issued by a competent judicial, investigative or governmental authority.',
				'title_ar' => 'مشاركة البيانات مع الآخرين',
				'body_ar'  => 'التوصيل إلى العملاء يتم حاليًا من خلال فريق مناديب تابع لنا مباشرة، ولذلك لا نقوم بتسليم بيانات طلبك إلى شركة شحن خارجية لمجرد تنفيذ التوصيل، ما دام هذا النظام هو المعمول به لدينا.

ومع ذلك، قد نستخدم خدمات تقنية من أطراف أخرى تكون ضرورية لتشغيل نشاطنا أو الموقع مثل:

- خدمات استضافة الموقع والخوادم.
- خدمات النسخ الاحتياطي.
- خدمات الأمن والحماية التقنية.
- أدوات تشغيل الموقع.
- خدمات التحليل وقياس الأداء، إذا كانت مفعّلة.
- خدمات التواصل التي يختار العميل استخدامها مثل واتساب.
- مزودي خدمات تقنية آخرين عند الضرورة لتشغيل الخدمة.

وفي هذه الحالات، نحرص على ألا نتيح لهذه الجهات سوى البيانات اللازمة لأداء الخدمة المطلوبة، وأن تتم معالجة البيانات في الحدود المسموح بها قانونًا وباستخدام الضمانات المناسبة.

نحن لا نبيع بياناتك الشخصية، ولا نؤجرها، ولا نتبادل قوائم العملاء مع جهات أخرى لأغراضها التسويقية الخاصة.

كما قد نضطر إلى الإفصاح عن بعض البيانات إذا أصبح ذلك مطلوبًا بموجب القانون أو تنفيذًا لحكم قضائي أو أمر رسمي صادر عن جهة قضائية أو تحقيقية أو حكومية مختصة.',
			),
			array(
				'num'      => '08',
				'title_en' => 'TRANSFERRING OR PROCESSING DATA OUTSIDE EGYPT',
				'body_en'  => 'Some technical services we use, such as hosting services or external technical tools, may rely on servers located outside the Arab Republic of Egypt.

If the use of one of these services results in the transfer or processing of personal data outside Egypt, we will handle this in accordance with the requirements, safeguards and procedures imposed by applicable personal data protection laws and regulations.

This section must reflect the technical services actually used on the website.',
				'title_ar' => 'نقل أو معالجة البيانات خارج مصر',
				'body_ar'  => 'قد تعتمد بعض الخدمات التقنية التي نستخدمها، مثل خدمات الاستضافة أو الأدوات التقنية الخارجية، على خوادم موجودة خارج جمهورية مصر العربية.

إذا ترتب على استخدام إحدى هذه الخدمات نقل أو معالجة بيانات شخصية خارج مصر، فسنتعامل مع ذلك وفقًا للمتطلبات والضمانات والإجراءات التي يفرضها قانون حماية البيانات الشخصية واللوائح والقرارات المنظمة لذلك.

ويجب أن تعكس هذه الفقرة الخدمات التقنية المستخدمة فعليًا في الموقع.',
			),
			array(
				'num'      => '09',
				'title_en' => 'COOKIES',
				'body_en'  => 'The website uses cookies and similar technologies according to the functions enabled on it.

These may include:

NECESSARY COOKIES

These are cookies required for the operation of essential website functions, such as:

- Retaining shopping cart contents.
- Saving certain user preferences.
- Maintaining website security and integrity.
- Enabling certain functions necessary to complete an order.

Disabling these cookies may cause parts of the website not to function properly or may prevent you from completing an order.

ANALYTICS AND PERFORMANCE COOKIES

If we enable visitor analytics tools, cookies or technical identifiers may be used to help us understand how visitors use the website and improve its performance.

We will not describe such information as completely anonymous unless the technology actually used provides that level of anonymity.

CONTROLLING COOKIES

You can control or delete cookies through your browser settings.

We may also provide a tool for managing cookie preferences on the website where this is required or appropriate according to the tools used.',
				'title_ar' => 'ملفات تعريف الارتباط Cookies',
				'body_ar'  => 'يستخدم الموقع ملفات تعريف ارتباط وتقنيات مشابهة بحسب الوظائف المفعّلة عليه.

وقد تشمل:

ملفات ضرورية

وهي الملفات اللازمة لتشغيل وظائف الموقع الأساسية، مثل:

- الاحتفاظ بمحتويات عربة التسوق.
- حفظ بعض تفضيلات المستخدم.
- الحفاظ على سلامة وأمن الموقع.
- تمكين بعض الوظائف الضرورية لإتمام الطلب.

قد يؤدي تعطيل هذه الملفات إلى عدم عمل أجزاء من الموقع بصورة صحيحة أو عدم القدرة على إتمام الطلب.

ملفات التحليل وقياس الأداء

إذا قمنا بتفعيل أدوات لتحليل الزيارات، فقد تستخدم ملفات تعريف ارتباط أو معرفات تقنية لمساعدتنا على فهم كيفية استخدام الزوار للموقع وتحسين أدائه.

لن نصف هذه البيانات بأنها مجهولة تمامًا إلا إذا كانت التقنية المستخدمة تحقق ذلك بالفعل.

التحكم في Cookies

يمكنك التحكم في ملفات تعريف الارتباط أو حذفها من خلال إعدادات متصفحك.

وقد نوفر أيضًا أداة لإدارة اختيارات ملفات تعريف الارتباط على الموقع متى كان ذلك مطلوبًا أو مناسبًا وفقًا للأدوات المستخدمة.',
			),
			array(
				'num'      => '10',
				'title_en' => 'MARKETING COMMUNICATIONS',
				'body_en'  => 'We may give you the option to receive messages about:

- Offers.
- Discounts.
- New products.
- News related to Lily Original Lenses.

Where prior consent is legally required for direct electronic marketing, we will not send such messages until we have obtained your valid and explicit consent.

Your consent to marketing is entirely optional and is not required to complete an order or receive customer service.

You may withdraw your consent or object to receiving marketing messages at any time by:

- Contacting Customer Service at 01060760098.
- Contacting us through WhatsApp.
- Using an unsubscribe option included in a message, where available.

Your request to stop receiving marketing messages will be respected without affecting your orders or your right to receive our services.

We may continue to send necessary non-marketing communications, such as order confirmation, delivery updates or responses to complaints, where they are necessary to provide the service.',
				'title_ar' => 'الرسائل التسويقية',
				'body_ar'  => 'قد نتيح لك اختيار تلقي رسائل حول:

- العروض.
- الخصومات.
- المنتجات الجديدة.
- الأخبار المتعلقة بـ Lily Original Lenses.

لن نرسل إليك رسائل تسويق إلكتروني مباشر عندما يكون الحصول على موافقتك المسبقة مطلوبًا إلا بعد حصولنا على موافقة صريحة وصحيحة منك.

موافقتك على التسويق اختيارية بالكامل، ولا يشترط تقديمها لتنفيذ طلبك أو الحصول على خدمة العملاء.

ويمكنك سحب موافقتك أو الاعتراض على تلقي الرسائل التسويقية في أي وقت، وذلك من خلال:

- التواصل مع خدمة العملاء على 01060760098.
- مراسلتنا عبر واتساب.
- استخدام وسيلة إلغاء الاشتراك الموجودة في الرسالة، عندما تكون متاحة.

وسيتم احترام طلب إلغاء الرسائل التسويقية دون أن يؤثر ذلك على طلباتك أو حقك في الحصول على الخدمة.

وقد نستمر في إرسال الرسائل الضرورية غير التسويقية، مثل رسائل تأكيد الطلب أو التوصيل أو الرد على شكوى، طالما كانت لازمة لتقديم الخدمة.',
			),
			array(
				'num'      => '11',
				'title_en' => 'HOW LONG DO WE KEEP YOUR DATA?',
				'body_en'  => 'We do not retain personal data for longer than necessary to achieve the purpose for which it was collected, while taking into account any retention periods required by law.

We may retain order-related information for the period necessary to:

- Fulfill the order.
- Complete delivery.
- Provide after-sales service.
- Handle complaints.
- Address manufacturing defects or exchanges where applicable.
- Establish or evidence transactions where necessary.
- Meet applicable commercial, accounting, tax and legal requirements.

Once there is no longer a legitimate need to retain the information, it may be deleted, anonymized or otherwise handled appropriately to prevent its use for unlawful purposes, unless retention is required by law.

The retention period may vary depending on the type of data and the purpose for which it was collected.',
				'title_ar' => 'مدة الاحتفاظ بالبيانات',
				'body_ar'  => 'لا نحتفظ ببياناتك الشخصية لمدة أطول من اللازم لتحقيق الغرض الذي جُمعت من أجله مع مراعاة أي مدد يفرضها القانون.

قد نحتفظ ببيانات الطلب طوال الفترة اللازمة من أجل:

- تنفيذ الطلب.
- التوصيل.
- خدمة ما بعد البيع.
- معالجة الشكاوى.
- التعامل مع عيوب الصناعة أو الاستبدال إن وجد.
- إثبات المعاملات عند الضرورة.
- الوفاء بالمتطلبات التجارية والمحاسبية والضريبية والقانونية السارية.

وبعد انتهاء الحاجة المشروعة للاحتفاظ بالبيانات، يتم حذفها أو إخفاء هوية صاحبها أو اتخاذ الإجراء المناسب لمنع استخدامها في أغراض غير مشروعة، ما لم يكن الاحتفاظ بها مطلوبًا بموجب القانون.

وقد تختلف مدة الاحتفاظ بحسب نوع البيانات والغرض الذي جُمعت من أجله.',
			),
			array(
				'num'      => '12',
				'title_en' => 'YOUR RIGHTS IN RELATION TO YOUR DATA',
				'body_en'  => 'Subject to applicable law and requirements, you may have a number of rights in relation to your personal data, including:

- Knowing what personal data we hold about you.
- Requesting access to that data in accordance with the law.
- Requesting correction or updating of inaccurate or incomplete data.
- Requesting deletion of your data where you have the right to do so.
- Withdrawing consent previously given for processing that relies on your consent.
- Objecting to certain processing activities where permitted by law.
- Requesting restriction of processing in cases provided for by law.
- Stopping marketing communications at any time.
- Submitting a complaint concerning how your data is handled through the channels provided by law and the competent supervisory authority.

In some cases, we may not be able to fulfill a deletion request, such as where a legal or accounting obligation requires us to retain certain information for a specific period. Where applicable, we will explain this when handling your request.',
				'title_ar' => 'حقوقك المتعلقة ببياناتك',
				'body_ar'  => 'وفقًا للقانون والضوابط المطبقة، قد يكون لك عدد من الحقوق المتعلقة ببياناتك الشخصية، ومنها:

- معرفة البيانات الشخصية الموجودة لدينا عنك.
- طلب الاطلاع عليها وفقًا للقانون.
- طلب تصحيح أو تحديث البيانات غير الصحيحة أو غير المكتملة.
- طلب حذف بياناتك عندما يكون لك الحق في ذلك.
- سحب الموافقة التي سبق أن منحتها لمعالجة تعتمد على موافقتك.
- الاعتراض على بعض عمليات المعالجة عندما يسمح القانون بذلك.
- طلب تقييد معالجة بياناتك في الحالات المقررة قانونًا.
- إيقاف الرسائل التسويقية في أي وقت.
- تقديم شكوى بشأن طريقة التعامل مع بياناتك وفقًا للطرق التي يتيحها القانون والجهة الرقابية المختصة.

قد لا نتمكن من تنفيذ طلب الحذف في بعض الحالات، مثل وجود التزام قانوني أو محاسبي يفرض علينا الاحتفاظ ببعض البيانات لفترة محددة، وسنوضح ذلك عند التعامل مع الطلب إذا انطبق.',
			),
			array(
				'num'      => '13',
				'title_en' => 'HOW TO EXERCISE YOUR RIGHTS',
				'body_en'  => 'You can submit a request relating to your personal data through:

Customer Service & WhatsApp:

01060760098

We may need to verify your identity before carrying out certain requests in order to ensure that your information is not disclosed or modified based on a request made by an unauthorized person.

We will handle requests according to the periods and procedures provided for by applicable laws and regulations.',
				'title_ar' => 'كيفية ممارسة حقوقك',
				'body_ar'  => 'يمكنك إرسال طلب يتعلق ببياناتك الشخصية من خلال:

خدمة العملاء وواتساب:

01060760098

قد نحتاج إلى التحقق من هويتك قبل تنفيذ بعض الطلبات، وذلك للتأكد من عدم تسليم بياناتك أو تعديلها بناءً على طلب شخص آخر غير مخول بذلك.

سنتعامل مع الطلبات وفقًا للمدد والإجراءات التي يقررها القانون واللوائح المنظمة.',
			),
			array(
				'num'      => '14',
				'title_en' => 'COMPLAINTS',
				'body_en'  => 'If you have an objection or complaint regarding how your personal data is collected, used or protected, we encourage you to contact us first through the communication channels stated in this policy so that we can review the matter and respond.

You may also have the right, in cases provided for by law, to submit a complaint to the Egyptian Personal Data Protection Center (PDPC) in accordance with the procedures and channels determined by the relevant authority.',
				'title_ar' => 'الشكاوى',
				'body_ar'  => 'إذا كان لديك أي اعتراض أو شكوى بشأن طريقة جمع أو استخدام أو حماية بياناتك، فنرجو التواصل معنا أولًا عبر وسائل الاتصال الموضحة في هذه السياسة حتى نتمكن من مراجعة المشكلة والرد عليها.

كما يكون لك الحق، في الحالات التي يقررها القانون، في التقدم بشكوى إلى مركز حماية البيانات الشخصية المصري (PDPC) وفق الإجراءات والقنوات التي يحددها المركز.',
			),
			array(
				'num'      => '15',
				'title_en' => 'DATA SECURITY',
				'body_en'  => 'We take appropriate and reasonable organizational and technical measures to protect personal data against risks such as:

- Unauthorized access.
- Unlawful use.
- Unauthorized modification.
- Loss or damage.
- Unauthorized disclosure.

We also limit access to personal data to persons whose work requires such access.

However, no method of transmitting or storing information over the internet can be guaranteed to be completely secure. We therefore work continuously to reduce risks and take appropriate precautions.',
				'title_ar' => 'أمن البيانات',
				'body_ar'  => 'نتخذ إجراءات تنظيمية وتقنية مناسبة ومعقولة لحماية البيانات الشخصية من مخاطر مثل:

- الوصول غير المصرح به.
- الاستخدام غير المشروع.
- التعديل غير المصرح به.
- الفقد أو التلف.
- الإفصاح غير المسموح به.

كما نقصر الوصول إلى البيانات الشخصية على الأشخاص الذين تتطلب طبيعة عملهم ذلك.

ومع ذلك، لا توجد وسيلة لنقل أو تخزين البيانات عبر الإنترنت يمكن ضمان أمانها بنسبة 100%، ولذلك نعمل باستمرار على تقليل المخاطر واتخاذ الاحتياطات المناسبة.',
			),
			array(
				'num'      => '16',
				'title_en' => 'DATA INCIDENTS AND BREACHES',
				'body_en'  => 'If a security incident involving personal data occurs, we will handle it in accordance with applicable legal requirements, including taking steps to limit its effects and making any notifications required to competent authorities or affected individuals where legally required.',
				'title_ar' => 'حوادث واختراقات البيانات',
				'body_ar'  => 'إذا وقع حادث أمني يتعلق ببيانات شخصية، فسنتعامل معه وفقًا للمتطلبات القانونية المعمول بها، بما في ذلك اتخاذ إجراءات الحد من آثاره وإجراء الإخطارات اللازمة للجهات المختصة أو للأشخاص المتأثرين عندما يكون ذلك مطلوبًا قانونًا.',
			),
			array(
				'num'      => '17',
				'title_en' => 'CHILDREN\'S PRIVACY',
				'body_en'  => 'The Lily Original Lenses website is intended for individuals aged eighteen years or older.

We do not knowingly seek to independently collect personal data from persons below this age.

If we become aware that personal data relating to a person under the age of eighteen has been collected without the necessary consent or other legal basis, we will take appropriate steps to address the situation, including deleting the data where required.

Where a transaction is carried out under the supervision of a parent or legal representative, it must comply with applicable legal requirements relating to minors\' personal data.',
				'title_ar' => 'خصوصية القاصرين',
				'body_ar'  => 'موقع Lily Original Lenses موجه للأشخاص الذين أتموا سن الثامنة عشرة.

لا نستهدف عن قصد جمع بيانات شخصية من أشخاص دون هذه السن بصورة مستقلة.

إذا علمنا بأن بيانات شخص دون الثامنة عشرة قد تم جمعها دون الموافقة أو الأساس القانوني اللازم، فسنتخذ الإجراءات المناسبة للتعامل معها، بما في ذلك حذفها عندما يكون ذلك مطلوبًا.

وفي الحالات التي تتم فيها معاملة تحت إشراف ولي الأمر أو الممثل القانوني، يجب أن تتم وفقًا للمتطلبات القانونية المتعلقة ببيانات القاصرين.',
			),
			array(
				'num'      => '18',
				'title_en' => 'EXTERNAL LINKS AND SERVICES',
				'body_en'  => 'The website may contain links or ways to access services operated by other parties, such as WhatsApp or social media platforms.

When you move to an external website or service, any information you provide there may be subject to that party\'s own privacy policy.

This policy does not mean that we control the privacy policies or practices of external websites and services. We therefore recommend reviewing their policies when using them.',
				'title_ar' => 'الروابط والخدمات الخارجية',
				'body_ar'  => 'قد يحتوي الموقع على روابط أو وسائل انتقال إلى خدمات تابعة لأطراف أخرى، مثل واتساب أو منصات اجتماعية.

عند انتقالك إلى خدمة أو موقع خارجي، قد تخضع البيانات التي تقدمها هناك لسياسة الخصوصية الخاصة بذلك الطرف.

لا تعني هذه السياسة أننا نتحكم في سياسات الخصوصية أو ممارسات المواقع والخدمات الخارجية، ولذلك ننصحك بمراجعة سياساتهم عند استخدامها.',
			),
			array(
				'num'      => '19',
				'title_en' => 'ACCURACY OF THE INFORMATION YOU PROVIDE',
				'body_en'  => 'Please make sure that the information you provide when placing an order is accurate and up to date, especially:

- Your name.
- Phone number.
- Delivery address.
- Product details.
- Prescription power or size, where applicable.

Providing inaccurate information may result in delays, an inability to deliver the order or preparation of a product with unsuitable specifications.

The customer remains responsible for the accuracy of any prescription power or medical specification entered.',
				'title_ar' => 'دقة البيانات التي تقدمها',
				'body_ar'  => 'يرجى التأكد من أن البيانات التي تقدمها عند تنفيذ الطلب صحيحة وحديثة خصوصًا:

- الاسم.
- رقم الهاتف.
- عنوان التوصيل.
- تفاصيل المنتج.
- درجة النظر أو المقاس، إن وجدت.

قد يؤدي تقديم بيانات غير صحيحة إلى تأخير الطلب أو عدم القدرة على توصيله أو تجهيز منتج بمواصفات غير مناسبة.

ويظل العميل مسؤولًا عن صحة درجة النظر أو المواصفات الطبية التي يقوم بإدخالها.',
			),
			array(
				'num'      => '20',
				'title_en' => 'CHANGES TO THIS PRIVACY POLICY',
				'body_en'  => 'We may update this policy from time to time due to:

- Changes in how the service operates.
- New website features.
- Changes to the technical tools used.
- Changes to legal or regulatory requirements.

If any change results in the use of your data for a new purpose that requires your consent, we will not rely solely on your continued use of the website. We will request the required consent separately before beginning that processing.',
				'title_ar' => 'التغييرات على سياسة الخصوصية',
				'body_ar'  => 'قد نقوم بتحديث هذه السياسة من وقت إلى آخر بسبب:

- تغيير طريقة عمل الخدمة.
- إضافة خصائص جديدة للموقع.
- تغيير الأدوات التقنية المستخدمة.
- تغيير المتطلبات القانونية أو التنظيمية.

إذا ترتب على أي تعديل استخدام بياناتك لغرض جديد يتطلب الحصول على موافقتك، فلن نعتمد فقط على استمرار استخدامك للموقع، بل سنطلب الموافقة المطلوبة بصورة منفصلة قبل البدء في هذه المعالجة.',
			),
			array(
				'num'      => '21',
				'title_en' => 'CONTACT US',
				'body_en'  => 'For any question about this policy or how we handle your personal data, you can contact us through:

Lily Original Lenses – Lily

Customer Service & WhatsApp:

01060760098

Customer Service:

Available throughout the day

Website:

lilylenses.com',
				'title_ar' => 'التواصل معنا',
				'body_ar'  => 'لأي سؤال بشأن هذه السياسة أو طريقة التعامل مع بياناتك الشخصية، يمكنك التواصل معنا من خلال:

Lily Original Lenses – ليلي

خدمة العملاء وواتساب:

01060760098

مواعيد خدمة العملاء:

متاحة على مدار اليوم

الموقع الإلكتروني:

lilylenses.com',
			),
		),
	);

	return $content;
}

/**
 * Default Privacy settings (empty = use the approved content above).
 *
 * @return array
 */
function lily_privacy_settings_defaults() {
	$defaults = array(
		'privacy_hero_title'    => '',
		'privacy_hero_title_ar' => '',
		'privacy_hero_intro'    => '',
		'privacy_hero_intro_ar' => '',
	);

	for ( $i = 1; $i <= 21; $i++ ) {
		$defaults[ "privacy_{$i}_title" ]    = '';
		$defaults[ "privacy_{$i}_title_ar" ] = '';
		$defaults[ "privacy_{$i}_body" ]     = '';
		$defaults[ "privacy_{$i}_body_ar" ]  = '';
	}

	return $defaults;
}

/**
 * Sanitize Privacy settings.
 *
 * @param array $raw Raw submitted values.
 * @return array
 */
function lily_sanitize_privacy_settings( array $raw ) {
	$data = array();

	$data['privacy_hero_title']    = isset( $raw['privacy_hero_title'] ) ? sanitize_text_field( $raw['privacy_hero_title'] ) : '';
	$data['privacy_hero_title_ar'] = isset( $raw['privacy_hero_title_ar'] ) ? sanitize_text_field( $raw['privacy_hero_title_ar'] ) : '';
	$data['privacy_hero_intro']    = isset( $raw['privacy_hero_intro'] ) ? sanitize_textarea_field( $raw['privacy_hero_intro'] ) : '';
	$data['privacy_hero_intro_ar'] = isset( $raw['privacy_hero_intro_ar'] ) ? sanitize_textarea_field( $raw['privacy_hero_intro_ar'] ) : '';

	for ( $i = 1; $i <= 21; $i++ ) {
		$data[ "privacy_{$i}_title" ]    = isset( $raw[ "privacy_{$i}_title" ] ) ? sanitize_text_field( $raw[ "privacy_{$i}_title" ] ) : '';
		$data[ "privacy_{$i}_title_ar" ] = isset( $raw[ "privacy_{$i}_title_ar" ] ) ? sanitize_text_field( $raw[ "privacy_{$i}_title_ar" ] ) : '';
		$data[ "privacy_{$i}_body" ]     = isset( $raw[ "privacy_{$i}_body" ] ) ? sanitize_textarea_field( $raw[ "privacy_{$i}_body" ] ) : '';
		$data[ "privacy_{$i}_body_ar" ]  = isset( $raw[ "privacy_{$i}_body_ar" ] ) ? sanitize_textarea_field( $raw[ "privacy_{$i}_body_ar" ] ) : '';
	}

	return wp_parse_args( $data, lily_privacy_settings_defaults() );
}

/**
 * Render the Privacy Page tab panels on the existing Lily admin page.
 *
 * @param array $settings Current settings.
 */
function lily_render_privacy_fields( $settings ) {
	$approved = lily_privacy_approved_content();

	echo '<section id="lily-tab-privacy" class="lily-admin-panel"><h2>' . esc_html__( 'Privacy Page', 'lily' ) . '</h2>';
	echo '<p class="description">' . esc_html__( 'Every field below is optional. Leave anything blank to keep the approved Privacy content.', 'lily' ) . '</p>';

	echo '<h3>' . esc_html__( 'Privacy Page — English', 'lily' ) . '</h3>';
	echo '<p class="description">' . esc_html__( 'Shown to English visitors.', 'lily' ) . '</p>';
	lily_admin_text_field( $settings, 'privacy_hero_title', esc_html__( 'Hero Title', 'lily' ) );
	lily_admin_textarea_field( $settings, 'privacy_hero_intro', esc_html__( 'Hero Introduction', 'lily' ) );

	echo '<h3>' . esc_html__( 'Privacy Sections — English', 'lily' ) . '</h3>';
	for ( $i = 1; $i <= 21; $i++ ) {
		$fallback = isset( $approved['sections'][ $i - 1 ] ) ? $approved['sections'][ $i - 1 ] : array();
		echo '<fieldset class="lily-link-field"><legend>' . esc_html( sprintf( __( 'Section %d', 'lily' ), $i ) ) . ' — ' . esc_html( $fallback['title_en'] ?? '' ) . '</legend>';
		printf( '<input type="text" name="lily_homepage[privacy_%1$d_title]" value="%2$s" placeholder="%3$s">', absint( $i ), esc_attr( $settings[ "privacy_{$i}_title" ] ?? '' ), esc_attr__( 'Section heading', 'lily' ) );
		printf( '<textarea name="lily_homepage[privacy_%1$d_body]" rows="6" placeholder="%2$s">%3$s</textarea>', absint( $i ), esc_attr__( 'Section content (blank line = new paragraph, "- " = bullet)', 'lily' ), esc_textarea( $settings[ "privacy_{$i}_body" ] ?? '' ) );
		echo '</fieldset>';
	}

	echo '<h3>' . esc_html__( 'Privacy Page', 'lily' ) . ' — ' . esc_html__( 'العربية', 'lily' ) . '</h3>';
	echo '<p class="description">' . esc_html__( 'Shown on Arabic pages only. Leave any field blank to reuse its English text.', 'lily' ) . '</p>';
	lily_admin_text_field( $settings, 'privacy_hero_title_ar', sprintf( __( '%s (Arabic)', 'lily' ), esc_html__( 'Hero Title', 'lily' ) ) );
	lily_admin_textarea_field( $settings, 'privacy_hero_intro_ar', sprintf( __( '%s (Arabic)', 'lily' ), esc_html__( 'Hero Introduction', 'lily' ) ) );

	echo '<h3>' . esc_html__( 'Privacy Sections', 'lily' ) . ' — ' . esc_html__( 'العربية', 'lily' ) . '</h3>';
	for ( $i = 1; $i <= 21; $i++ ) {
		$fallback = isset( $approved['sections'][ $i - 1 ] ) ? $approved['sections'][ $i - 1 ] : array();
		echo '<fieldset class="lily-link-field"><legend>' . esc_html( sprintf( __( 'Section %d (Arabic)', 'lily' ), $i ) ) . ' — ' . esc_html( $fallback['title_ar'] ?? '' ) . '</legend>';
		printf( '<input type="text" name="lily_homepage[privacy_%1$d_title_ar]" value="%2$s" placeholder="%3$s">', absint( $i ), esc_attr( $settings[ "privacy_{$i}_title_ar" ] ?? '' ), esc_attr__( 'Section heading (Arabic)', 'lily' ) );
		printf( '<textarea name="lily_homepage[privacy_%1$d_body_ar]" rows="6" placeholder="%2$s">%3$s</textarea>', absint( $i ), esc_attr__( 'Section content (Arabic)', 'lily' ), esc_textarea( $settings[ "privacy_{$i}_body_ar" ] ?? '' ) );
		echo '</fieldset>';
	}

	echo '</section>';
}
