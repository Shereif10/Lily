<?php
/**
 * Lily Shipping & Delivery Policy dashboard fields (slug: shipping-delivery-policy).
 *
 * Reuses the existing Lily settings system (same option, same save flow,
 * same admin helpers as terms/faq/contact): the ship_* keys live inside
 * `lily_homepage_settings` and render as a "Shipping Page" tab on the
 * existing Lily admin page. The page template reuses the Terms &
 * Conditions markup and CSS classes, so no new design system is created.
 *
 * Single source of truth: lily_shipping_approved_content() holds the
 * complete approved English + Arabic policy verbatim. The template uses it
 * as fallback and the initial DB population copies it into the dashboard
 * fields, so the brand owner can edit both languages without code.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Complete approved Shipping & Delivery Policy content (verbatim source).
 *
 * Section 3 is split around the delivery-fee table: body holds the
 * paragraphs before the table, body_after holds the paragraphs after it.
 * Step-like "1." lines are stored as separate paragraphs (blank-line
 * separated) so the shared Terms body renderer keeps them readable
 * without touching its logic.
 *
 * @return array
 */
function lily_shipping_approved_content() {
	static $content = null;

	if ( null !== $content ) {
		return $content;
	}

	$content = array(
		'hero_eyebrow_en' => 'SHIPPING',
		'hero_eyebrow_ar' => 'الشحن',
		'hero_title_en'   => 'Shipping & Delivery Policy',
		'hero_title_ar'   => 'سياسة الشحن والتوصيل',
		'hero_intro_en'   => 'Everything you need to know about delivery coverage, shipping fees, expected delivery times and receiving your order from Lily.',
		'hero_intro_ar'   => 'كل ما تحتاجين إلى معرفته عن مناطق التوصيل، ورسوم الشحن، ومدة التوصيل المتوقعة، واستلام طلبك من ليلي.',
		'th_range_en'     => 'RANGE',
		'th_range_ar'     => 'النطاق',
		'th_areas_en'     => 'GOVERNORATES / AREAS',
		'th_areas_ar'     => 'المحافظات والمناطق',
		'th_fee_en'       => 'DELIVERY FEE',
		'th_fee_ar'       => 'سعر التوصيل',
		'sections'        => array(
			array(
				'num'      => '01',
				'title_en' => 'DELIVERY COVERAGE',
				'body_en'  => 'We offer delivery across all governorates of the Arab Republic of Egypt, including most remote and border areas, subject to accessibility.

Delivery time and cost may vary depending on the governorate, area, distance and road conditions.

If your address is in an area that cannot be reached directly, we will contact you before confirming the order to agree on the nearest suitable location or delivery method.

International shipping outside the Arab Republic of Egypt is not currently available.',
				'title_ar' => 'مناطق التغطية',
				'body_ar'  => 'نقدم خدمة التوصيل داخل جميع محافظات جمهورية مصر العربية، بما يشمل معظم المناطق النائية والحدودية وفقًا لإمكانية الوصول إليها.

وقد تختلف مدة وتكلفة التوصيل بحسب المحافظة والمنطقة والمسافة وظروف الطريق.

إذا كان عنوانك في منطقة يتعذر الوصول إليها مباشرة، سنتواصل معك قبل تأكيد الطلب للاتفاق على أقرب نقطة أو طريقة مناسبة للتسليم.

ولا نوفر حاليًا خدمة الشحن خارج جمهورية مصر العربية.',
			),
			array(
				'num'      => '02',
				'title_en' => 'EXPECTED DELIVERY TIME',
				'body_en'  => 'The delivery timeframe begins once the order has been confirmed by our Customer Service team, not from the moment the order is submitted through the website.

The expected delivery timeframe or delivery window will be communicated to the customer when the order is confirmed.

Delivery times may be affected in certain cases by circumstances beyond reasonable control, including:

- Seasonal periods and high order volumes.
- Public holidays and vacations.
- Weather conditions.
- Road conditions.
- Road or area closures.
- Public events or exceptional decisions.
- Difficulty reaching certain remote areas.

If we expect a significant delay beyond the agreed delivery timeframe, we will make reasonable efforts to contact you and inform you.

Your legal rights in the event of a delivery delay remain fully protected.',
				'title_ar' => 'مدة التوصيل المتوقعة',
				'body_ar'  => 'تبدأ مدة التوصيل من وقت تأكيد الطلب من فريق خدمة العملاء، وليس من وقت إرسال الطلب على الموقع.

ويتم إبلاغ العميل بمدة أو نطاق التوصيل المتوقع عند تأكيد الطلب.

قد تتأثر مواعيد التوصيل في بعض الحالات بعوامل خارجة عن السيطرة المعقولة، مثل:

- المواسم وفترات زيادة الطلب.
- الأعياد والعطلات.
- الأحوال الجوية.
- ظروف الطرق.
- إغلاق بعض الطرق أو المناطق.
- الأحداث العامة أو القرارات الاستثنائية.
- صعوبة الوصول لبعض المناطق النائية.

وفي حالة توقع تأخير جوهري عن الموعد المتفق عليه، نحاول التواصل مع العميل وإبلاغه بذلك.

وتظل الحقوق القانونية للعميل في حالة التأخير في التسليم محفوظة.',
			),
			array(
				'num'        => '03',
				'title_en'   => 'DELIVERY FEES',
				'body_en'    => 'Delivery fees are added to the value of the products, and the customer will be informed of the total order value and delivery fee before the order is confirmed.

Free shipping is not generally available at this time unless a specific offer expressly states otherwise.

Current delivery fees:',
				'body_after_en' => 'All prices are in Egyptian Pounds.

Delivery prices may be updated in the future due to changes in operating costs. However, the delivery fee confirmed with the customer when the order is confirmed will not change for that same order.

If an address is not clearly covered by the table above, the delivery cost will be determined and communicated to the customer before the order is confirmed.',
				'title_ar'   => 'أسعار التوصيل',
				'body_ar'    => 'تضاف مصاريف التوصيل إلى قيمة المنتجات، ويتم إبلاغ العميل بإجمالي قيمة الطلب ومصاريف التوصيل قبل تأكيده.

لا تتوفر حاليًا خدمة شحن مجاني بصورة عامة، إلا إذا تم الإعلان عن عرض ينص صراحةً على خلاف ذلك.

الأسعار الحالية هي:',
				'body_after_ar' => 'جميع الأسعار بالجنيه المصري.

قد يتم تحديث أسعار التوصيل مستقبلًا نتيجة تغير تكاليف التشغيل، ولكن السعر الذي يتم تأكيده مع العميل عند تأكيد الطلب لا يتغير بعد ذلك بالنسبة للطلب نفسه.

وفي حالة وجود عنوان غير مدرج بوضوح ضمن الجدول، يتم تحديد تكلفة التوصيل وإبلاغ العميل بها قبل تأكيد الطلب.',
			),
			array(
				'num'      => '04',
				'title_en' => 'ORDER PREPARATION',
				'body_en'  => 'We accept orders through the website throughout the day.

After an order is submitted:

1. The order details are reviewed.

2. Our Customer Service team may contact the customer when necessary to confirm the information and address.

3. The order total and delivery fee are confirmed.

4. Order preparation begins after confirmation.

5. The order is handed over to the delivery team according to the operating schedule.

We aim to begin preparing the order on the day it is confirmed and have it ready to enter the next available delivery cycle, often starting from the following day.

Some orders or areas may require additional time depending on product availability and the delivery route.',
				'title_ar' => 'تجهيز الطلب',
				'body_ar'  => 'نستقبل الطلبات من خلال الموقع على مدار اليوم.

بعد إرسال الطلب:

1. تتم مراجعة بياناته.

2. يتواصل فريق خدمة العملاء مع العميل عند الحاجة لتأكيد البيانات والعنوان.

3. يتم تأكيد السعر ومصاريف التوصيل.

4. يبدأ تجهيز الطلب بعد تأكيده.

5. يتم تسليمه إلى فريق التوصيل وفق جدول التشغيل.

نستهدف بدء تجهيز الطلب في يوم تأكيده وتجهيزه للخروج للتوصيل في أقرب دورة توصيل متاحة، وغالبًا بدءًا من اليوم التالي.

وقد تحتاج بعض الطلبات أو المناطق إلى وقت أطول بحسب توافر المنتج وخط سير التوصيل.',
			),
			array(
				'num'      => '05',
				'title_en' => 'WORKING DAYS',
				'body_en'  => 'The website and Customer Service team accept orders throughout the day.

Friday:

The delivery team does not operate on its regular schedule, while Customer Service and order reception remain available.

Delivery is not normally carried out during:

- Eid Al-Fitr.
- Eid Al-Adha.
- New Year\'s Day.

Different schedules may be announced during official holidays or exceptional circumstances.

Days when the delivery service is not operating are not counted as working days when a delivery timeframe is expressed in working days.',
				'title_ar' => 'أيام العمل',
				'body_ar'  => 'يستقبل الموقع وخدمة العملاء الطلبات على مدار اليوم.

يوم الجمعة:

لا يعمل فريق التوصيل بصورة اعتيادية، بينما تستمر خدمة العملاء واستقبال الطلبات.

ولا يتم التوصيل بصورة اعتيادية خلال:

- عيد الفطر.
- عيد الأضحى.
- يوم رأس السنة الميلادية.

وقد يتم الإعلان عن مواعيد مختلفة خلال الإجازات الرسمية أو الظروف الاستثنائية.

ولا تُحتسب الأيام التي لا تعمل فيها خدمة التوصيل ضمن أيام العمل عند تحديد موعد التسليم، متى كان الموعد محددًا بأيام العمل.',
			),
			array(
				'num'      => '06',
				'title_en' => 'CONTACT BEFORE DELIVERY',
				'body_en'  => 'The delivery representative may contact the customer before arriving at the address to confirm:

- The availability of the customer or an authorized person to receive the order.
- The accuracy of the address.
- The location when necessary.
- A suitable meeting or delivery point when required.

Please make sure that you provide a correct and reachable phone number.

Providing an alternative phone number is also recommended, although optional, in case the primary number cannot be reached.',
				'title_ar' => 'التواصل قبل التوصيل',
				'body_ar'  => 'قد يتواصل المندوب مع العميل قبل الوصول إلى العنوان للتأكد من:

- وجود العميل أو شخص مخول بالاستلام.
- صحة العنوان.
- تحديد الموقع عند الحاجة.
- الاتفاق على نقطة مناسبة للاستلام.

لذلك يرجى التأكد من إدخال رقم هاتف صحيح ومتاح.

كما يُفضل توفير رقم بديل بصورة اختيارية إذا كان من المحتمل تعذر الوصول إلى الرقم الأساسي.',
			),
			array(
				'num'      => '07',
				'title_en' => 'RECEIVING AND INSPECTING THE ORDER',
				'body_en'  => 'Because contact lenses are products of a personal and hygienic nature, the product\'s original packaging should remain sealed until the customer decides to keep the product. This helps protect product safety and supports the application of the exchange and return policy in accordance with the law.

When the order arrives, the customer may check:

- The external condition of the delivery package.
- Whether there is any visible damage caused during transport.
- The number of products.
- The type of products ordered.
- The color, shade or size stated on the packaging, where this can be checked without breaking the original product seal.

The outer shipping wrapper or bag may be opened in the presence of the delivery representative to check the contents of the order, provided that the original sealed contact lens packaging is not opened and the product is not used.

The contact lens packaging itself must not be opened, its safety seal must not be broken, and sealed hygienic packaging must not be opened for testing or inspection before deciding whether to accept the order.

If, during inspection, the order clearly contains a product different from the one ordered or there is an obvious issue with the shipment, please inform the delivery representative and contact us immediately.

Receiving the shipment does not mean that the customer waives any legal right relating to a hidden defect or non-conformity that cannot reasonably be discovered during a normal inspection.',
				'title_ar' => 'الاستلام ومعاينة الطلب',
				'body_ar'  => 'نظرًا إلى أن العدسات اللاصقة من المنتجات ذات الطبيعة الصحية والشخصية، يجب الحفاظ على العبوة الأصلية للمنتج مغلقة بختمها الأصلي إلى أن يقرر العميل الاحتفاظ بها، وذلك حفاظًا على سلامة المنتج وإمكانية تطبيق سياسة الاستبدال والاسترجاع وفقًا للقانون.

عند وصول الطلب، يجوز للعميل التحقق من:

- سلامة طرد التوصيل من الخارج.
- عدم وجود تلف ظاهر ناتج عن النقل.
- عدد المنتجات.
- نوع المنتجات المطلوبة.
- اللون أو الدرجة أو المقاس المدون على العبوة، متى أمكن التحقق منها دون كسر الختم الأصلي للمنتج.

يمكن فتح غلاف أو كيس الشحن الخارجي بحضور المندوب للتحقق من محتويات الطلب، بشرط عدم فتح العبوة الأصلية المختومة للعدسات أو استخدام المنتج.

لا يجوز فتح عبوة العدسات نفسها أو كسر ختم الأمان أو فتح العبوات الصحية المغلقة بغرض التجربة أو المعاينة قبل اتخاذ قرار الاستلام.

إذا تبين عند المعاينة أن الطلب يحتوي على منتج مختلف بوضوح عن المنتج المطلوب أو توجد مشكلة ظاهرة في الشحنة، يرجى إبلاغ المندوب والتواصل معنا فورًا.

ولا يُعد استلام الشحنة تنازلًا من العميل عن أي حق قانوني يتعلق بعيب خفي أو عدم مطابقة لا يمكن اكتشافه بالمعاينة المعتادة.',
			),
			array(
				'num'      => '08',
				'title_en' => 'PAYMENT ON DELIVERY',
				'body_en'  => 'The order value and delivery fees are paid using the method agreed upon when the order is confirmed.

Available payment methods may include:

- Cash on delivery.
- InstaPay.
- Electronic wallet.

The delivery representative will never request your passwords, OTP verification codes or online banking login details.',
				'title_ar' => 'الدفع عند الاستلام',
				'body_ar'  => 'يتم سداد قيمة الطلب ومصاريف التوصيل بالطريقة التي تم الاتفاق عليها عند تأكيد الطلب.

وقد تشمل وسائل الدفع:

- النقد عند الاستلام.
- إنستا باي.
- المحفظة الإلكترونية.

لا يطلب المندوب من العميل أي كلمات مرور أو رموز تحقق OTP أو بيانات دخول بنكية.',
			),
			array(
				'num'      => '09',
				'title_en' => 'IF WE CANNOT REACH THE CUSTOMER',
				'body_en'  => 'The delivery representative will make reasonable attempts to contact the customer in order to complete the delivery. More than one contact attempt may be made depending on the delivery route and circumstances.

If the customer cannot be reached and the order cannot be delivered, the order may be returned to the warehouse.

We will then contact the customer to determine whether they would like to:

- Arrange another delivery attempt.
- Update the delivery address.
- Cancel the order, where cancellation is available.

If another delivery attempt is requested because the customer provided incorrect information or was unavailable at the agreed time, an additional delivery fee may apply. The customer will be informed of any new fee before the order is sent again.',
				'title_ar' => 'تعذر التواصل مع العميل',
				'body_ar'  => 'يحاول المندوب التواصل مع العميل بصورة معقولة من أجل إتمام التوصيل، وقد يتم إجراء أكثر من محاولة اتصال بحسب ظروف خط السير.

إذا تعذر التواصل مع العميل بصورة كاملة ولم يمكن تسليم الطلب، فقد تتم إعادة الطلب إلى المخزن.

نتواصل بعد ذلك مع العميل لتحديد ما إذا كان يرغب في:

- إعادة محاولة التوصيل.
- تعديل بيانات العنوان.
- إلغاء الطلب، متى كان ذلك متاحًا.

وفي حالة طلب إعادة التوصيل بسبب بيانات غير صحيحة قدمها العميل أو تعذر استلامه في الموعد المتفق عليه، قد يتم احتساب مصاريف محاولة توصيل جديدة وإبلاغ العميل بها قبل إعادة إرسال الطلب.',
			),
			array(
				'num'      => '10',
				'title_en' => 'REFUSAL TO ACCEPT THE ORDER',
				'body_en'  => 'If the customer refuses to accept the order, the situation will be handled according to the reason for the refusal.

IF THE REFUSAL IS DUE TO AN ERROR BY LILY

Examples include:

- Sending a product different from the one ordered.
- An error in quantity.
- Visible damage to the shipment.
- A difference between the delivered price and the final price confirmed with the customer.

In such cases, the customer will not be responsible for any additional costs resulting from that error.

IF THE REFUSAL IS NOT DUE TO AN ERROR BY LILY

If the correct order reaches the agreed address and the customer refuses to accept it without any error in the order, or cancels the order after it has already been sent out for delivery, the customer may be responsible for the actual delivery cost that was communicated and agreed upon when the order was confirmed, within the limits permitted by law.

This does not limit or reduce any mandatory right granted to the customer under Egyptian Consumer Protection Law, including rights relating to distance contracts.',
				'title_ar' => 'رفض الاستلام',
				'body_ar'  => 'إذا رفض العميل استلام الطلب، يتم التعامل مع الحالة وفقًا لسبب الرفض.

إذا كان سبب الرفض خطأ من جانب ليلي

مثل:

- إرسال منتج مختلف عن المطلوب.
- خطأ في الكمية.
- وجود تلف ظاهر في الشحنة.
- اختلاف السعر عن السعر النهائي الذي تم تأكيده مع العميل.

فلا يتحمل العميل مصروفات إضافية ناتجة عن هذا الخطأ.

إذا كان الرفض دون خطأ من جانب ليلي

إذا وصل الطلب الصحيح إلى العنوان المتفق عليه ورفض العميل الاستلام دون وجود خطأ في الطلب، أو قام بإلغاء الطلب بعد خروجه بالفعل للتوصيل، فقد يتحمل تكلفة التوصيل الفعلية التي تم إبلاغه بها والموافقة عليها عند تأكيد الطلب، وذلك في الحدود التي يسمح بها القانون.

ولا يترتب على هذا البند الانتقاص من أي حق إلزامي يمنحه قانون حماية المستهلك للعميل، بما في ذلك الحقوق المتعلقة بالتعاقد عن بُعد.',
			),
			array(
				'num'      => '11',
				'title_en' => 'DELIVERY DELAYS',
				'body_en'  => 'We aim to meet the delivery date or delivery timeframe communicated to the customer when the order is confirmed.

If the delivery of the order is significantly delayed beyond the agreed timeframe, please contact Customer Service.

In cases of delay, the customer retains the rights provided under Egyptian Consumer Protection Law, including the right to withdraw from the contract without bearing costs where provided for by law.',
				'title_ar' => 'التأخر في التوصيل',
				'body_ar'  => 'نحرص على الالتزام بموعد أو نطاق التوصيل الذي يتم إبلاغ العميل به عند تأكيد الطلب.

إذا تأخر تسليم الطلب عن الموعد المتفق عليه بصورة جوهرية، يرجى التواصل مع خدمة العملاء.

ويكون للعميل في حالات التأخير الحقوق التي يقررها قانون حماية المستهلك المصري، بما في ذلك حقه في الرجوع عن التعاقد دون تحمل النفقات في الحالات التي ينص عليها القانون.',
			),
			array(
				'num'      => '12',
				'title_en' => 'DAMAGE TO THE SHIPMENT DURING DELIVERY',
				'body_en'  => 'If the package arrives in a condition that clearly indicates significant damage during transport, please:

- Inform the delivery representative before opening the original product packaging.
- Photograph the condition of the package where possible.
- Contact Customer Service immediately.

We will review the situation and take the appropriate action without affecting any of the customer\'s legal rights.',
				'title_ar' => 'تلف الشحنة أثناء التوصيل',
				'body_ar'  => 'إذا وصل الطرد بحالة خارجية تشير بوضوح إلى تعرضه لتلف شديد أثناء النقل، يرجى:

- إخطار المندوب قبل فتح العبوات الأصلية.
- تصوير حالة الطرد إن أمكن.
- التواصل مع خدمة العملاء فورًا.

وسنتولى مراجعة الحالة واتخاذ الإجراء المناسب دون الانتقاص من الحقوق القانونية للعميل.',
			),
			array(
				'num'      => '13',
				'title_en' => 'INCORRECT OR MISSING PRODUCT',
				'body_en'  => 'If, after receiving the order, it becomes clear that:

- A product is missing from the order.
- A different product was sent.
- A different shade, color or size was sent from the one confirmed.

Please do not open the product\'s original packaging and contact us as soon as possible.

If the error is confirmed to be on our side, we will bear the cost of correcting it in accordance with the law and our Exchange & Return Policy.',
				'title_ar' => 'المنتج الخطأ أو الناقص',
				'body_ar'  => 'إذا تبين بعد الاستلام أن:

- منتجًا غير موجود في الطلب.
- تم إرسال منتج مختلف.
- تم إرسال درجة أو لون أو مقاس مختلف عما تم تأكيده.

يرجى عدم فتح العبوة الأصلية للمنتج والتواصل معنا في أقرب وقت ممكن.

إذا ثبت أن الخطأ من جانبنا، نتحمل تكلفة تصحيحه وفقًا للقانون وسياسة الاستبدال والاسترجاع.',
			),
			array(
				'num'      => '14',
				'title_en' => 'MANUFACTURING DEFECTS',
				'body_en'  => 'If a problem is discovered after receiving the product and it is suspected to be a manufacturing defect, please contact Customer Service in accordance with the Exchange & Return Policy.

Payment for the order or the departure of the delivery representative does not remove or limit the customer\'s legal rights relating to a defective or non-conforming product.',
				'title_ar' => 'عيوب الصناعة',
				'body_ar'  => 'إذا ظهرت مشكلة يُشتبه في كونها عيب صناعة بعد استلام المنتج، فيرجى التواصل مع خدمة العملاء وفقًا لسياسة الاستبدال والاسترجاع.

ولا يؤدي دفع قيمة الطلب أو مغادرة المندوب إلى إسقاط الحقوق القانونية للعميل المتعلقة بالمنتج المعيب أو غير المطابق.',
			),
			array(
				'num'      => '15',
				'title_en' => 'CANCELLING AN ORDER BEFORE IT GOES OUT FOR DELIVERY',
				'body_en'  => 'The customer may contact us to request cancellation of an order.

If the order is cancelled before it goes out for delivery and no delivery expenses have actually been incurred, no delivery fee will be charged to the customer.

If the order has already been sent out with the delivery representative, delivery costs will be handled in accordance with the Refusal to Accept the Order section and applicable law.',
				'title_ar' => 'إلغاء الطلب قبل خروجه للتوصيل',
				'body_ar'  => 'يمكن للعميل التواصل معنا لطلب إلغاء الطلب.

إذا تم الإلغاء قبل خروج الطلب للتوصيل ولم تكن هناك مصروفات توصيل قد تم تنفيذها فعليًا، فلا يتم تحصيل مصاريف توصيل من العميل.

أما بعد خروج الطلب مع المندوب، فيتم التعامل مع مصاريف التوصيل وفقًا لبند رفض الاستلام وأحكام القانون.',
			),
			array(
				'num'      => '16',
				'title_en' => 'ANOTHER DELIVERY ATTEMPT',
				'body_en'  => 'If delivery cannot be completed on the first attempt for a reason attributable to the customer, another delivery attempt may be arranged.

The customer will be informed of any new delivery fees before the second attempt is made.

No additional fee will be charged where the issue resulted from an error by Lily or the delivery team.',
				'title_ar' => 'إعادة محاولة التوصيل',
				'body_ar'  => 'إذا تعذر التسليم في المرة الأولى لسبب يرجع إلى العميل، يمكن الاتفاق على محاولة توصيل أخرى.

ويتم إبلاغ العميل بأي مصاريف توصيل جديدة قبل تنفيذ المحاولة الثانية.

ولا يتم فرض أي رسوم جديدة بسبب خطأ من جانب ليلي أو فريق التوصيل.',
			),
			array(
				'num'      => '17',
				'title_en' => 'ORDER TRACKING',
				'body_en'  => 'An electronic shipment tracking service with a tracking number is not currently available.

You can check the status of your order at any time by contacting us and providing your order number or the phone number used for the order.

Customer Service & WhatsApp:

01060760098',
				'title_ar' => 'تتبع الطلب',
				'body_ar'  => 'لا تتوفر حاليًا خدمة تتبع إلكتروني برقم شحنة.

يمكنك معرفة حالة طلبك في أي وقت من خلال التواصل معنا وتقديم رقم الطلب أو رقم الهاتف المسجل به.

خدمة العملاء وواتساب:

01060760098',
			),
			array(
				'num'      => '18',
				'title_en' => 'COLLECTION FROM LILY',
				'body_en'  => 'Collection of orders directly from Lily\'s premises is not currently available.

All orders placed through the website are delivered using the available and announced delivery methods.',
				'title_ar' => 'الاستلام من مقر ليلي',
				'body_ar'  => 'لا تتوفر حاليًا خدمة استلام الطلبات من مقر المتجر.

جميع الطلبات التي يتم تنفيذها من خلال الموقع يتم تسليمها وفق وسائل التوصيل المتاحة والمعلنة.',
			),
			array(
				'num'      => '19',
				'title_en' => 'INTERNATIONAL SHIPPING',
				'body_en'  => 'We do not currently offer delivery outside the Arab Republic of Egypt.',
				'title_ar' => 'الشحن الدولي',
				'body_ar'  => 'لا نوفر حاليًا خدمة توصيل الطلبات خارج جمهورية مصر العربية.',
			),
			array(
				'num'      => '20',
				'title_en' => 'RELATIONSHIP TO THE EXCHANGE & RETURN POLICY',
				'body_en'  => 'This page governs shipping and delivery procedures only.

Customer rights after receiving an order, and policies relating to:

- Exchanges.
- Returns.
- Withdrawal from purchase.
- Opened products.
- Manufacturing defects.
- Non-conforming products.

are governed by the Exchange & Return Policy published on the website and by the provisions of Egyptian Consumer Protection Law.

If there is any conflict between this policy and a mandatory right granted to the consumer by law, the legal provision takes priority.',
				'title_ar' => 'العلاقة بسياسة الاستبدال والاسترجاع',
				'body_ar'  => 'تنظم هذه الصفحة إجراءات الشحن والتوصيل فقط.

وتخضع حقوق العميل بعد الاستلام وسياسات:

- الاستبدال.
- الاسترجاع.
- العدول عن الشراء.
- المنتجات المفتوحة.
- عيوب الصناعة.
- المنتجات غير المطابقة.

لسياسة الاستبدال والاسترجاع المنشورة على الموقع وأحكام قانون حماية المستهلك المصري.

وفي حالة وجود تعارض بين هذه السياسة وحق إلزامي يمنحه القانون للمستهلك، تكون الأولوية للنص القانوني.',
			),
			array(
				'num'      => '21',
				'title_en' => 'CHANGES TO THIS SHIPPING POLICY',
				'body_en'  => 'We may update delivery coverage areas, delivery fees or delivery procedures from time to time.

Any update will be published on this page.

Changes to prices or terms will not affect an order that has already been confirmed under the agreed price and delivery conditions, unless otherwise agreed with the customer or where a change is required by law.',
				'title_ar' => 'تعديل سياسة الشحن',
				'body_ar'  => 'قد نقوم بتعديل مناطق التغطية أو مصاريف أو إجراءات التوصيل من وقت إلى آخر.

يتم نشر أي تحديث على هذه الصفحة.

ولا يؤثر تعديل الأسعار أو الشروط على طلب سبق تأكيده بالسعر وشروط التوصيل المتفق عليها، إلا إذا اتفق مع العميل على خلاف ذلك أو كان التعديل واجبًا بموجب القانون.',
			),
			array(
				'num'      => '22',
				'title_en' => 'CONTACT US',
				'body_en'  => 'For any question about shipping or the status of your order:

Lily – Original Lily Lenses

Customer Service & WhatsApp:

01060760098

Website:

lilylenses.com

Customer Service:

Available throughout the day',
				'title_ar' => 'التواصل معنا',
				'body_ar'  => 'لأي استفسار بشأن الشحن أو حالة طلبك:

ليلي – Lily Original Lenses

خدمة العملاء وواتساب:

01060760098

الموقع الإلكتروني:

lilylenses.com

الخدمة:

متاحة على مدار اليوم',
			),
		),
		'table'         => array(
			array(
				'range_en' => 'Cairo & Giza',
				'areas_en' => 'Greater Cairo areas and main Giza districts',
				'fee_en'   => 'EGP 70',
				'range_ar' => 'القاهرة والجيزة',
				'areas_ar' => 'مناطق القاهرة الكبرى وأحياء الجيزة الرئيسية',
				'fee_ar'   => '70 ج.م',
			),
			array(
				'range_en' => 'New Cities',
				'areas_en' => '6th of October, Fifth Settlement, Badr, 15th of May, Al Rehab, Madinaty, El Shorouk, Hadayek October',
				'fee_en'   => 'EGP 80',
				'range_ar' => 'المدن الجديدة',
				'areas_ar' => 'أكتوبر، التجمع، بدر، مايو، الرحاب، مدينتي، الشروق، حدائق أكتوبر',
				'fee_ar'   => '80 ج.م',
			),
			array(
				'range_en' => 'Giza Outskirts',
				'areas_en' => 'Manshiyat Al Qanater, Abu El Nomros, Al Hawamdeya, Al Badrashin, Shabramant, Al Saf, Al Ayat, Atfih',
				'fee_en'   => 'EGP 110',
				'range_ar' => 'ضواحي الجيزة',
				'areas_ar' => 'منشأة القناطر، أبو النمرس، الحوامدية، البدرشين، شبرامنت، الصف، العياط، أطفيح',
				'fee_ar'   => '110 ج.م',
			),
			array(
				'range_en' => 'Lower Egypt & Canal Region',
				'areas_en' => 'Alexandria, Qalyubia, Dakahlia, Sharqia, Gharbia, Menoufia, Beheira, Kafr El Sheikh, Damietta, Port Said, Ismailia, Suez',
				'fee_en'   => 'EGP 90',
				'range_ar' => 'الوجه البحري والقناة',
				'areas_ar' => 'الإسكندرية، القليوبية، الدقهلية، الشرقية، الغربية، المنوفية، البحيرة، كفر الشيخ، دمياط، بورسعيد، الإسماعيلية، السويس',
				'fee_ar'   => '90 ج.م',
			),
			array(
				'range_en' => 'New Damietta',
				'areas_en' => 'New Damietta City',
				'fee_en'   => 'EGP 100',
				'range_ar' => 'دمياط الجديدة',
				'areas_ar' => 'مدينة دمياط الجديدة',
				'fee_ar'   => '100 ج.م',
			),
			array(
				'range_en' => 'Fayoum',
				'areas_en' => 'All cities and districts',
				'fee_en'   => 'EGP 90',
				'range_ar' => 'الفيوم',
				'areas_ar' => 'جميع المراكز والمدن',
				'fee_ar'   => '90 ج.م',
			),
			array(
				'range_en' => 'Beni Suef & Minya',
				'areas_en' => 'All cities and districts',
				'fee_en'   => 'EGP 100',
				'range_ar' => 'بني سويف والمنيا',
				'areas_ar' => 'جميع المراكز والمدن',
				'fee_ar'   => '100 ج.م',
			),
			array(
				'range_en' => 'Assiut, Sohag, Qena & Luxor',
				'areas_en' => 'All cities and districts',
				'fee_en'   => 'EGP 120',
				'range_ar' => 'أسيوط وسوهاج وقنا والأقصر',
				'areas_ar' => 'جميع المراكز والمدن',
				'fee_ar'   => '120 ج.م',
			),
			array(
				'range_en' => 'Aswan',
				'areas_en' => 'All cities and districts',
				'fee_en'   => 'EGP 150',
				'range_ar' => 'أسوان',
				'areas_ar' => 'جميع المراكز والمدن',
				'fee_ar'   => '150 ج.م',
			),
			array(
				'range_en' => 'New Valley & Marsa Matrouh',
				'areas_en' => 'Kharga, Dakhla, other districts, Marsa Matrouh City and its surrounding districts',
				'fee_en'   => 'EGP 150',
				'range_ar' => 'الوادي الجديد ومرسى مطروح',
				'areas_ar' => 'الخارجة، الداخلة، باقي المراكز، مدينة مطروح والمراكز التابعة',
				'fee_ar'   => '150 ج.م',
			),
			array(
				'range_en' => 'Border & Coastal Governorates and Areas',
				'areas_en' => 'North Sinai, South Sinai, Sharm El Sheikh, Red Sea Governorate, Hurghada and the North Coast',
				'fee_en'   => 'EGP 170',
				'range_ar' => 'المحافظات والمناطق الحدودية والساحلية',
				'areas_ar' => 'شمال وجنوب سيناء، شرم الشيخ، البحر الأحمر، الغردقة، الساحل الشمالي',
				'fee_ar'   => '170 ج.م',
			),
		),
	);

	return $content;
}

/**
 * Ensure the Shipping & Delivery page exists (slug: shipping-delivery-policy).
 *
 * Reuses an existing page with the same slug and never overwrites content.
 * The old shipping-policy page is left untouched.
 */
function lily_maybe_create_shipping_delivery_page() {
	$existing = get_page_by_path( 'shipping-delivery-policy' );

	if ( $existing instanceof WP_Post ) {
		return;
	}

	wp_insert_post(
		array(
			'post_title'     => 'Shipping & Delivery Policy',
			'post_name'      => 'shipping-delivery-policy',
			'post_content'   => '',
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		)
	);
}
add_action( 'init', 'lily_maybe_create_shipping_delivery_page', 20 );

/**
 * Default Shipping settings (empty = use the approved content above).
 *
 * @return array
 */
function lily_shipping_policy_settings_defaults() {
	$defaults = array(
		'ship_hero_eyebrow'    => '',
		'ship_hero_eyebrow_ar' => '',
		'ship_hero_title'      => '',
		'ship_hero_title_ar'   => '',
		'ship_hero_intro'      => '',
		'ship_hero_intro_ar'   => '',
		'ship_th_range'        => '',
		'ship_th_range_ar'     => '',
		'ship_th_areas'        => '',
		'ship_th_areas_ar'     => '',
		'ship_th_fee'          => '',
		'ship_th_fee_ar'       => '',
		'ship_3_after'         => '',
		'ship_3_after_ar'      => '',
	);

	for ( $i = 1; $i <= 22; $i++ ) {
		$defaults[ "ship_{$i}_title" ]    = '';
		$defaults[ "ship_{$i}_title_ar" ] = '';
		$defaults[ "ship_{$i}_body" ]     = '';
		$defaults[ "ship_{$i}_body_ar" ]  = '';
	}

	for ( $r = 1; $r <= 11; $r++ ) {
		$defaults[ "ship_row_{$r}_range" ]     = '';
		$defaults[ "ship_row_{$r}_range_ar" ]  = '';
		$defaults[ "ship_row_{$r}_areas" ]     = '';
		$defaults[ "ship_row_{$r}_areas_ar" ]  = '';
		$defaults[ "ship_row_{$r}_fee" ]       = '';
		$defaults[ "ship_row_{$r}_fee_ar" ]    = '';
	}

	return $defaults;
}

/**
 * Sanitize Shipping settings.
 *
 * @param array $raw Raw submitted values.
 * @return array
 */
function lily_sanitize_shipping_policy_settings( array $raw ) {
	$data = array();

	$text_fields = array(
		'ship_hero_eyebrow',
		'ship_hero_eyebrow_ar',
		'ship_hero_title',
		'ship_hero_title_ar',
		'ship_th_range',
		'ship_th_range_ar',
		'ship_th_areas',
		'ship_th_areas_ar',
		'ship_th_fee',
		'ship_th_fee_ar',
	);

	foreach ( $text_fields as $field ) {
		$data[ $field ] = isset( $raw[ $field ] ) ? sanitize_text_field( $raw[ $field ] ) : '';
	}

	$data['ship_hero_intro']    = isset( $raw['ship_hero_intro'] ) ? sanitize_textarea_field( $raw['ship_hero_intro'] ) : '';
	$data['ship_hero_intro_ar'] = isset( $raw['ship_hero_intro_ar'] ) ? sanitize_textarea_field( $raw['ship_hero_intro_ar'] ) : '';
	$data['ship_3_after']       = isset( $raw['ship_3_after'] ) ? sanitize_textarea_field( $raw['ship_3_after'] ) : '';
	$data['ship_3_after_ar']    = isset( $raw['ship_3_after_ar'] ) ? sanitize_textarea_field( $raw['ship_3_after_ar'] ) : '';

	for ( $i = 1; $i <= 22; $i++ ) {
		$data[ "ship_{$i}_title" ]    = isset( $raw[ "ship_{$i}_title" ] ) ? sanitize_text_field( $raw[ "ship_{$i}_title" ] ) : '';
		$data[ "ship_{$i}_title_ar" ] = isset( $raw[ "ship_{$i}_title_ar" ] ) ? sanitize_text_field( $raw[ "ship_{$i}_title_ar" ] ) : '';
		$data[ "ship_{$i}_body" ]     = isset( $raw[ "ship_{$i}_body" ] ) ? sanitize_textarea_field( $raw[ "ship_{$i}_body" ] ) : '';
		$data[ "ship_{$i}_body_ar" ]  = isset( $raw[ "ship_{$i}_body_ar" ] ) ? sanitize_textarea_field( $raw[ "ship_{$i}_body_ar" ] ) : '';
	}

	for ( $r = 1; $r <= 11; $r++ ) {
		foreach ( array( 'range', 'areas', 'fee' ) as $col ) {
			$data[ "ship_row_{$r}_{$col}" ]    = isset( $raw[ "ship_row_{$r}_{$col}" ] ) ? sanitize_text_field( $raw[ "ship_row_{$r}_{$col}" ] ) : '';
			$data[ "ship_row_{$r}_{$col}_ar" ] = isset( $raw[ "ship_row_{$r}_{$col}_ar" ] ) ? sanitize_text_field( $raw[ "ship_row_{$r}_{$col}_ar" ] ) : '';
		}
	}

	return wp_parse_args( $data, lily_shipping_policy_settings_defaults() );
}

/**
 * Render the Shipping Page tab panels on the existing Lily admin page.
 *
 * @param array $settings Current settings.
 */
function lily_render_shipping_policy_fields( $settings ) {
	$approved = lily_shipping_approved_content();

	echo '<section id="lily-tab-shipping" class="lily-admin-panel"><h2>' . esc_html__( 'Shipping Page', 'lily' ) . '</h2>';
	echo '<p class="description">' . esc_html__( 'Every field below is optional. Leave anything blank to keep the approved Shipping content.', 'lily' ) . '</p>';

	echo '<h3>' . esc_html__( 'Shipping Page — English', 'lily' ) . '</h3>';
	echo '<p class="description">' . esc_html__( 'Shown to English visitors.', 'lily' ) . '</p>';
	lily_admin_text_field( $settings, 'ship_hero_eyebrow', esc_html__( 'Hero Eyebrow', 'lily' ) );
	lily_admin_text_field( $settings, 'ship_hero_title', esc_html__( 'Hero Title', 'lily' ) );
	lily_admin_textarea_field( $settings, 'ship_hero_intro', esc_html__( 'Hero Introduction', 'lily' ) );

	echo '<h3>' . esc_html__( 'Shipping Sections — English', 'lily' ) . '</h3>';
	for ( $i = 1; $i <= 22; $i++ ) {
		$fallback = isset( $approved['sections'][ $i - 1 ] ) ? $approved['sections'][ $i - 1 ] : array();
		echo '<fieldset class="lily-link-field"><legend>' . esc_html( sprintf( __( 'Section %d', 'lily' ), $i ) ) . ' — ' . esc_html( $fallback['title_en'] ?? '' ) . '</legend>';
		printf( '<input type="text" name="lily_homepage[ship_%1$d_title]" value="%2$s" placeholder="%3$s">', absint( $i ), esc_attr( $settings[ "ship_{$i}_title" ] ?? '' ), esc_attr__( 'Section heading', 'lily' ) );
		printf( '<textarea name="lily_homepage[ship_%1$d_body]" rows="6" placeholder="%2$s">%3$s</textarea>', absint( $i ), esc_attr__( 'Section content (blank line = new paragraph, "- " = bullet)', 'lily' ), esc_textarea( $settings[ "ship_{$i}_body" ] ?? '' ) );
		if ( 3 === $i ) {
			printf( '<textarea name="lily_homepage[ship_3_after]" rows="6" placeholder="%1$s">%2$s</textarea>', esc_attr__( 'Section 3 content shown after the delivery-fee table', 'lily' ), esc_textarea( $settings['ship_3_after'] ?? '' ) );
		}
		echo '</fieldset>';
	}

	echo '<h3>' . esc_html__( 'Delivery-Fee Table — English', 'lily' ) . '</h3>';
	lily_admin_text_field( $settings, 'ship_th_range', esc_html__( 'Column Heading: Range', 'lily' ) );
	lily_admin_text_field( $settings, 'ship_th_areas', esc_html__( 'Column Heading: Areas', 'lily' ) );
	lily_admin_text_field( $settings, 'ship_th_fee', esc_html__( 'Column Heading: Fee', 'lily' ) );
	for ( $r = 1; $r <= 11; $r++ ) {
		echo '<fieldset class="lily-link-field"><legend>' . esc_html( sprintf( __( 'Table Row %d', 'lily' ), $r ) ) . '</legend>';
		printf( '<input type="text" name="lily_homepage[ship_row_%1$d_range]" value="%2$s" placeholder="%3$s">', absint( $r ), esc_attr( $settings[ "ship_row_{$r}_range" ] ?? '' ), esc_attr__( 'Range', 'lily' ) );
		printf( '<input type="text" name="lily_homepage[ship_row_%1$d_areas]" value="%2$s" placeholder="%3$s">', absint( $r ), esc_attr( $settings[ "ship_row_{$r}_areas" ] ?? '' ), esc_attr__( 'Governorates / Areas', 'lily' ) );
		printf( '<input type="text" name="lily_homepage[ship_row_%1$d_fee]" value="%2$s" placeholder="%3$s">', absint( $r ), esc_attr( $settings[ "ship_row_{$r}_fee" ] ?? '' ), esc_attr__( 'Delivery Fee', 'lily' ) );
		echo '</fieldset>';
	}

	echo '<h3>' . esc_html__( 'Shipping Page', 'lily' ) . ' — ' . esc_html__( 'العربية', 'lily' ) . '</h3>';
	echo '<p class="description">' . esc_html__( 'Shown on Arabic pages only. Leave any field blank to reuse its English text.', 'lily' ) . '</p>';
	lily_admin_text_field( $settings, 'ship_hero_eyebrow_ar', sprintf( __( '%s (Arabic)', 'lily' ), esc_html__( 'Hero Eyebrow', 'lily' ) ) );
	lily_admin_text_field( $settings, 'ship_hero_title_ar', sprintf( __( '%s (Arabic)', 'lily' ), esc_html__( 'Hero Title', 'lily' ) ) );
	lily_admin_textarea_field( $settings, 'ship_hero_intro_ar', sprintf( __( '%s (Arabic)', 'lily' ), esc_html__( 'Hero Introduction', 'lily' ) ) );

	echo '<h3>' . esc_html__( 'Shipping Sections', 'lily' ) . ' — ' . esc_html__( 'العربية', 'lily' ) . '</h3>';
	for ( $i = 1; $i <= 22; $i++ ) {
		$fallback = isset( $approved['sections'][ $i - 1 ] ) ? $approved['sections'][ $i - 1 ] : array();
		echo '<fieldset class="lily-link-field"><legend>' . esc_html( sprintf( __( 'Section %d (Arabic)', 'lily' ), $i ) ) . ' — ' . esc_html( $fallback['title_ar'] ?? '' ) . '</legend>';
		printf( '<input type="text" name="lily_homepage[ship_%1$d_title_ar]" value="%2$s" placeholder="%3$s">', absint( $i ), esc_attr( $settings[ "ship_{$i}_title_ar" ] ?? '' ), esc_attr__( 'Section heading (Arabic)', 'lily' ) );
		printf( '<textarea name="lily_homepage[ship_%1$d_body_ar]" rows="6" placeholder="%2$s">%3$s</textarea>', absint( $i ), esc_attr__( 'Section content (Arabic)', 'lily' ), esc_textarea( $settings[ "ship_{$i}_body_ar" ] ?? '' ) );
		if ( 3 === $i ) {
			printf( '<textarea name="lily_homepage[ship_3_after_ar]" rows="6" placeholder="%1$s">%2$s</textarea>', esc_attr__( 'Section 3 content after the table (Arabic)', 'lily' ), esc_textarea( $settings['ship_3_after_ar'] ?? '' ) );
		}
		echo '</fieldset>';
	}

	echo '<h3>' . esc_html__( 'Delivery-Fee Table', 'lily' ) . ' — ' . esc_html__( 'العربية', 'lily' ) . '</h3>';
	lily_admin_text_field( $settings, 'ship_th_range_ar', sprintf( __( '%s (Arabic)', 'lily' ), esc_html__( 'Column Heading: Range', 'lily' ) ) );
	lily_admin_text_field( $settings, 'ship_th_areas_ar', sprintf( __( '%s (Arabic)', 'lily' ), esc_html__( 'Column Heading: Areas', 'lily' ) ) );
	lily_admin_text_field( $settings, 'ship_th_fee_ar', sprintf( __( '%s (Arabic)', 'lily' ), esc_html__( 'Column Heading: Fee', 'lily' ) ) );
	for ( $r = 1; $r <= 11; $r++ ) {
		echo '<fieldset class="lily-link-field"><legend>' . esc_html( sprintf( __( 'Table Row %d (Arabic)', 'lily' ), $r ) ) . '</legend>';
		printf( '<input type="text" name="lily_homepage[ship_row_%1$d_range_ar]" value="%2$s" placeholder="%3$s">', absint( $r ), esc_attr( $settings[ "ship_row_{$r}_range_ar" ] ?? '' ), esc_attr__( 'Range (Arabic)', 'lily' ) );
		printf( '<input type="text" name="lily_homepage[ship_row_%1$d_areas_ar]" value="%2$s" placeholder="%3$s">', absint( $r ), esc_attr( $settings[ "ship_row_{$r}_areas_ar" ] ?? '' ), esc_attr__( 'Governorates / Areas (Arabic)', 'lily' ) );
		printf( '<input type="text" name="lily_homepage[ship_row_%1$d_fee_ar]" value="%2$s" placeholder="%3$s">', absint( $r ), esc_attr( $settings[ "ship_row_{$r}_fee_ar" ] ?? '' ), esc_attr__( 'Delivery Fee (Arabic)', 'lily' ) );
		echo '</fieldset>';
	}

	echo '</section>';
}
