-- seed.sql data inserts (import into existing database)
SET FOREIGN_KEY_CHECKS=0;

INSERT INTO users (email,password_hash,full_name,is_admin) VALUES
('admin@scriptloaded.test','$2y$10$nUr5BC0FaYfuBYvuh8.WgOXHV68hg6dFU2ytUyG/tJ1/udDSGwbte','Scriptloaded Admin',1),
('creator@scriptloaded.test','$2y$10$0EZsvBjTUQl7MGzke1frf.JSDar3/pnLXV.xyy9kYYTJqCA1Iepsy','Alex Hartman',0);

INSERT INTO user_profiles (user_id,plan,avatar_url,location,website,bio) VALUES
(1,'Marketplace Admin','https://i.pravatar.cc/150?img=5','Lagos, Nigeria','https://scriptloaded.test','Oversees marketplace quality and launches.'),
(2,'Pro Creator','https://i.pravatar.cc/150?img=13','Berlin, Germany','https://alexhartman.dev','Product designer shipping premium UI kits and templates.');

INSERT INTO settings(`key`,`value`) VALUES
('site_name','Scriptloaded'),
('support_email','support@scriptloaded.test'),
('currency_rate_usd_to_ngn','1500');

INSERT INTO products (title,slug,short_description,long_description,preview_image,gallery,youtube_overview,youtube_install,live_preview_url,author_name,author_avatar,price_usd,price_ngn,category,tags,description_points,features,version,changelog,file_path,rating,reviews_count,downloads_count,is_active)
VALUES
('E-commerce Website Script','ecommerce-website-script','Launch a conversion-optimized storefront with automated order flows.','Complete Laravel + Tailwind storefront paired with an elegant admin panel.','https://lh3.googleusercontent.com/aida-public/AB6AXuCKgvZ07FdFHdW4gZhqwE_5lTNZp6YRH7JQMqXGXZUbyloQoIf9O3wsC87r7fFzc2N9JrqqRVbVqYwVt669MY7FW8BiL68ZbU_kPEbW-6FWJLlWVrGFcdQlBiRSjAHhx53JW_va5cZZUWB1WMN2Hdqsr4-JGoSgSZd2edSgjmJzrw4topToZp8CgkT1bG_3ZSFoxaiEjnpdK_BgWQ2q2ymP5mimv9NNAXR_DxSes-oSq6opSbDRSejwnbBy5rvvX7x7F4C5xZL4afrW','["https://lh3.googleusercontent.com/aida-public/AB6AXuAoEZef5_Kz07F2Hyqz9ADaie4ewAy5GWezXsNZ8wMWceD1xOQHx9vneippsQuD_6iI_kcwsI_0lGM6TJ9lKCflOu1PlrQ-dL8WCW92xhjcGTTsfGiNj4cx65r4REp-Qe3zMH9qib6kFrhRS-VJJQ8u82NL36YFN8zw7kNhKiICiVUuo37lcdVdVG--hWZ48olnLvBevzY8_UQ0DtXcrEEJ-8XkSgbM1TJfpEncNm-2ZJYerY4ct5ViQPvS3qoVTjo1_xnwfdLTmrCS","https://lh3.googleusercontent.com/aida-public/AB6AXuCSfGG9jhDe6TAYSuof3YGLLZDFf4UyuoItnt0S_ylznTKNdEVl0ioPfqoUZej9fkZnivxmX02Z51oW4zFROVKuukj0uHbtfirtfKJul-gFYd2_YwlvhN_yLYQ96WjASkFmMqQ0MhUwGa3Moxx3Tuop1xG5SNXUxWIfBqL9CXgCij5fBdVNaaQEa65eUq1UiaMwUXPM8BWMzPTnvvq0j_kUbHNwu_MyCj8IISNAggLpY28e8zMB2y10kZBMoQ5FLLFgNQyxUD0JGaTN"]','dQw4w9WgXcQ','dQw4w9WgXcQ','https://demo.scriptloaded.test/ecommerce','CodeCrafters','https://i.pravatar.cc/150?img=8',49.99,75000,'Scripts','["laravel","commerce","tailwind"]','["Full-stack storefront with admin analytics.","One-click product imports and email automation."]','["Responsive storefront","Stripe + Paystack integration","Automated fulfillment timelines","Order timeline with audit trail"]','1.2.0','[{"version":"1.2.0","date":"2025-10-15","items":["Added AI product copy","Improved checkout telemetry"]},{"version":"1.1.0","date":"2025-09-02","items":["New dark mode","Coupon fixes"]}]','/storage/products/ecommerce.zip',4.9,128,540,1),
('Pro SaaS Kit','pro-saas-kit','Starter kit for modern SaaS dashboards.','40+ modular dashboard blocks wired with chart presets.','https://lh3.googleusercontent.com/aida-public/AB6AXuBCeD3kjMaIWmJf9WGJXWblME5GUge6hm9AdPcZAUx_Ae9K-snTGh5fBUeek2F3EM0f0hQyfOcNXhJNzlb6COJBZnQ51AlbDgLKcwZFxbJLdXSExZ3oxLYCsyuW86w6JQBkBKHB86fDRppefJn2sWv4SvfkND5FcOrnhGQaS9a5-hUkoLCWmtPdUvR960cBQNUNVknU9sYU4pD9b_EcZ3fBLD8v2Piu_lMv2lleF4l7NjfQdtrqSalkQZyXKC51ZusR2KfvOV0BWfN6','["https://lh3.googleusercontent.com/aida-public/AB6AXuA5ppNcfX0aM2nVRJllT6yXXIFjZ0c1ILUjWtUYxuP1gmCGCmKTeLb8BJYVKrO50zRSQrK-bCSXeL3XrLM3d3cgTaFeldu1mxqBWR8PP11Vol1Zb0wychD_dRG_SLszjtTq53wErQBNBFCsa0lUNQk4Hckcbirhc-zIUANR8GEDICTO4DtAbIcmyF4awdo0LW31ofkaAH3_JJ2vYUhA0le9P6KNi2Li0cvaIngojTXPcbiAYhLDikliwuJd3rN_vb6QDHX34iATxA5O"]','dQw4w9WgXcQ','dQw4w9WgXcQ','https://demo.scriptloaded.test/saas','CodeCrafters','https://i.pravatar.cc/150?img=9',59.00,90000,'UI Kits','["react","charts","tailwind"]','["Production ready states","Carefully tuned typography"]','["40+ responsive blocks","Chart.js + Apex presets","Auth + billing screens","Command palette"]','2.0.0','[{"version":"2.0.0","date":"2025-09-30","items":["Figma token export","New chart animations"]}]','/storage/products/saas-kit.zip',4.8,86,310,1),
('Neomorphic Social UI Kit','neomorphic-social-ui-kit','Figma kit for futuristic social apps.','Layered components with auto-layout support.','https://lh3.googleusercontent.com/aida-public/AB6AXuB2k_cuQ48H9We-pO82_gLpv4a0KermFpCOnb6oofKM_z56KHLF6UJlQqW2CHxeIpr4lWIafYCh1OA_WoP4CbO6cHR-d-reIvMbZt9ZP2O91O0uJuMUvbN_vVwX4lfdRLtU1oLsojWR2Zwp9Bd-OcJgiZQ9EnSa9VU63V-Kkd8CFQipJV5QD0dckV5Pp6SC1dEvtHbXXAO8ujrNzdzKL5S_QifwB4U60r9VIyPB6JxMIx9Bk4iQc5CeIRsLc_9_NRBHzPaZVkv3C-T-','["https://lh3.googleusercontent.com/aida-public/AB6AXuCp6lW373wZNNafjHuxjPA3mTN9Uy5EezEJ1C0K7o1VlEpjKnpowZYQmUu_T0TQUP39xlNt9VuVNcOXKuUDD-1Q26AYfXvbxDqedERu0e4ZAntT9VWq5wztW2VaEjVXbFRFmdZVb_zpoz8Kp5zV0FMlSkmVQk0m5i4mnNm8ukTTfWBSi8TTnao-FkGAGlrICVS9DPA8aqyGPk0ZbQv_E1OGB66B4ruQpZgdLFgKn8J7bampum-fcqX1sxW4YSzHPiaD7KXJ8Q3w0874"]','dQw4w9WgXcQ','dQw4w9WgXcQ','https://figma.com/file/social-kit','UI Masters','https://i.pravatar.cc/150?img=15',49.00,73500,'UI Kits','["figma","light","dark"]','["Auto-layout ready","Desktop + mobile variants"]','["Neomorphic surfaces","Dark/light pairs","Prototype ready flows"]','1.0.0','[{"version":"1.0.0","date":"2025-08-12","items":["Initial release"]}]','/storage/products/social-kit.fig',5.0,64,190,1),
('DataViz Plugin','dataviz-plugin','WordPress analytics widgets with realtime cards.','Drop-in charts for marketing dashboards.','https://lh3.googleusercontent.com/aida-public/AB6AXuAxeAnZnH6hU7lHh9gJed2r9fRb0WDbNOxcIpwRGSpcCB9nQBv5YsrVr2zBtSMBVWEfx1q6OGwFOKNcwBq_cvhbjStEYxnYvdhO7H_dURzVJBJO75QPHd54ycLitauoHpVFisAnAL_EeyQfCx39JGn6dnm9FpU0m3ZPF0D1sKjVJJ9EkceLL8HDTUevT1C5fy7ygDRxTJD_696BvDaGNtDu8xdzzeQT4_9-0t486dkk9aA5kva1O3ql6YSbpYafo6QEX5e5W8Zb9llt','["https://lh3.googleusercontent.com/aida-public/AB6AXuDlyd_g7Gfmq74txdfmcigSysWl1lOOTjz7IkSk2wuN_8kkf5g_DCFfY_Ed1XdFzSKMKqJ2FJh93NLQEXS1eMlDBeNv82kx-ZpVABRywtQl8KV1MYMjo-wFxATzN5opmbdZnbVd1A1gBKoDwnFiC_nQ-IXK6xImxjw5RUTsuuYvxFhM8krP6_0RbtIWQHvxvfcHexEdIWgiENy-vZnYhFcn1Qm98lNB-tw2DU3Zdt_ZlCO7o5KuUOEcSvgSgJ-1Qdq9TXpzhoC2OQk5"]','dQw4w9WgXcQ','dQw4w9WgXcQ','https://demo.scriptloaded.test/dataviz','GraphWorks','https://i.pravatar.cc/150?img=21',29.00,42000,'Plugins','["wordpress","analytics"]','["Prebuilt widgets","Headless compatible"]','["Elementor blocks","Realtime API bridge"]','1.4.0','[{"version":"1.4.0","date":"2025-09-10","items":["Added GA4 connector","Cache warmups"]}]','/storage/products/dataviz.zip',4.7,47,260,1),
('E-Shop Mobile App','e-shop-mobile-app','React Native commerce starter with Paystack + Stripe.','Delightful onboarding screens with secure checkout.','https://lh3.googleusercontent.com/aida-public/AB6AXuB82HjIPh5qmpzLraL7W52gp202lTIXcS2knBStCRnD5WdGmzwpCiVKEiHxRpLcr8TKI1SnxtI7uECxYLfp29y0tTfVpJLp3hyssGQ7lAY4RJuMyyHDSj_dASW0vJm4NNe1XzmZ6r2VAjtbtGpw8nHuujfZo3p8fb5pr6QnjHCrOm0VOgbXg3sTui1r7eZQ06RawzO8lyU6aTw_5zQpXyT9oEus9HLBu7JEipQyZUl829rL218UBAaLj0AWRmEg6iQW6AIuBb-OMa6G','["https://lh3.googleusercontent.com/aida-public/AB6AXuC6DfaBV0snoVviC05kpt-7O_6_ty-IgoM0VKBsHDqN5GGbTLnbFiWtwxgNFt7BEjbmjRE42AzfEZz5zcBx0j0Xn4d3YVMvr_Cz7rXwsyLtpYSxgWTGf35EDnwOGE7qyHihPh1GdPUxyIBGvavlxE4ph4u_-zjB5wMGxfyqfDSzJVlXnBc-rsio5YJRzPnfLlmwPFiwUZdHcrloQg0y--z-pBpzNUGSdwNEIXJUz4B2y3OUuLd4cFlhCW8NJZXthgh5BqSibgmABjrA"]','dQw4w9WgXcQ','dQw4w9WgXcQ','https://demo.scriptloaded.test/app','AppWizards','https://i.pravatar.cc/150?img=17',120.00,180000,'Apps','["react-native","commerce"]','["Biometric checkout","Universal theming"]','["Expo friendly","Push ready","Analytics events"]','1.3.0','[{"version":"1.3.0","date":"2025-07-18","items":["Expo SDK 51","Faster product feed"]}]','/storage/products/e-shop-app.zip',4.6,72,140,1);

INSERT INTO orders (user_id,product_id,amount,currency,payment_gateway,gateway_ref,license_key,status,completed_at)
VALUES
(2,1,49.99,'USD','paystack','PSK-938221','SL-ECOM-001','completed','2025-10-26 10:12:00'),
(2,2,59.00,'USD','stripe','ST-552199','SL-SAAS-884','completed','2025-09-15 14:35:00'),
(2,5,120.00,'USD','stripe','ST-772314','SL-APP-443','completed','2025-08-02 09:05:00'),
(2,4,29.00,'USD','paystack','PSK-128811','SL-DATA-225','completed','2025-07-21 16:33:00');

INSERT INTO invoices (order_id,invoice_number,amount,currency,status,download_url) VALUES
(1,'INV-2048',49.99,'USD','paid','https://cdn.scriptloaded.test/invoices/INV-2048.pdf'),
(2,'INV-2037',59.00,'USD','paid','https://cdn.scriptloaded.test/invoices/INV-2037.pdf'),
(3,'INV-1994',120.00,'USD','paid','https://cdn.scriptloaded.test/invoices/INV-1994.pdf');

INSERT INTO billing_methods (user_id,brand,last4,exp_month,exp_year,cardholder,is_primary)
VALUES
(2,'Visa','4242',8,2026,'Alex Hartman',1),
(2,'Mastercard','5599',11,2025,'Alex Hartman',0);

INSERT INTO favorites (user_id,product_id) VALUES
(2,2),(2,3),(2,4);

INSERT INTO support_tickets (user_id,subject,message,status,priority,created_at,updated_at) VALUES
(2,'License key request','Need additional licenses for client install.','open','medium','2025-10-24 09:22:00','2025-10-24 10:05:00'),
(2,'Download token expired','Token expired before I could complete download.','in_progress','high','2025-10-20 08:10:00','2025-10-21 11:44:00'),
(2,'Payment receipt copy','Please resend invoice for accounting.','resolved','low','2025-09-28 12:35:00','2025-09-30 07:15:00');

INSERT INTO download_tokens (order_id,token,expires_at) VALUES
(1,'SLDW-ECOM-202510','2025-11-15 12:00:00'),
(2,'SLDW-SAAS-202509','2025-11-01 12:00:00'),
(3,'SLDW-APP-202508','2025-10-30 12:00:00'),
(4,'SLDW-DATA-202507','2025-10-20 12:00:00');

INSERT INTO user_notifications (user_id,pref_key,is_enabled) VALUES
(2,'product_updates',1),
(2,'promotions',0),
(2,'security',1);

INSERT INTO reviews (user_id,product_id,rating,comment) VALUES
(2,1,5,'Rock solid checkout and onboarding flow.'),
(2,2,5,'Great starting point for SaaS dashboards.'),
(2,4,4,'Charts are beautiful and easy to configure.');

SET FOREIGN_KEY_CHECKS=1;
