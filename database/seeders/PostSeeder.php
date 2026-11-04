<?php

namespace Database\Seeders;

use App\Models\Post;
use App\PostStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PostSeeder extends Seeder
{
    /**
     * Import the first two articles that were written for the website.
     * Running it again updates them rather than creating duplicates.
     */
    public function run(): void
    {
        foreach ($this->posts() as $post) {
            $cover = 'blog/'.$post['slug'].'.svg';
            Storage::disk('public')->put($cover, file_get_contents(__DIR__.'/blog-covers/'.$post['slug'].'.svg'));

            Post::updateOrCreate(['slug' => $post['slug']], [
                ...$post,
                'cover_path' => $cover,
                'status' => PostStatus::Published,
                'published_at' => '2026-11-04 09:00:00',
            ]);
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function posts(): array
    {
        return [
            [
                'slug' => 'website-vs-web-app-vs-mobile-app',
                'title' => 'Website, web app or mobile app: which does your business need?',
                'seo_title' => 'Website vs Web App vs Mobile App: Which Do You Need?',
                'description' => 'Not sure whether your business needs a website, a web app or a mobile app? A plain-English guide to the differences, costs and when to choose each one.',
                'category' => 'Planning',
                'tags' => ['Web apps', 'Mobile apps', 'Websites', 'Planning'],
                'cover_alt' => 'Illustration comparing a website in a browser, a web app dashboard and a mobile app on a phone',
                'service' => 'online-booking-websites',
                'body' => <<<'MD'
When business owners contact me, one of the first questions is: **do I need a website, a web app or a mobile app?** The answer decides your budget, your timeline and how your customers will use what you build, so it is worth getting right before any work begins.

This guide explains the difference between the three in plain English, with simple rules to help you choose.

## What is the difference?

### A website

A website mainly **shares information**: who you are, what you offer, your prices and how to contact you. Visitors read it, and the goal is usually to turn them into calls, bookings or enquiries. Most small businesses start here.

### A web app

A web app runs in the browser like a website, but people **do things** in it: log in, book appointments, manage orders, upload files or see a dashboard. It has a database and accounts behind it. Online booking systems, customer portals and internal admin tools are all web apps.

### A mobile app

A mobile app is installed from the App Store or Google Play. It can use phone features such as push notifications, the camera and location, and it works best for things customers use often, such as ordering, loyalty schemes or tracking.

## When a website is enough

- You want new customers to find you on Google and get in touch
- Your services and prices change only now and then
- Customers contact you by phone, email or a simple form
- You want to look professional and trustworthy quickly

For most of these businesses, a fast, SEO-friendly website is the best first step. See my [business website package](/services/small-business-website-design) for what that includes.

## When you need a web app

- Customers need to book, order or pay online without calling you
- You or your staff need to log in and manage data
- You are spending hours on admin that software could do
- You need different access for customers, staff and managers

A common example is online booking: customers pick a service and time themselves, and you get confirmations and reminders automatically. Read more about [booking websites](/services/online-booking-websites).

## When a mobile app makes sense

- Customers will use it often, ideally every week
- Push notifications would bring them back, such as offers or order updates
- You need phone features like the camera, GPS or offline use
- You already have a website or web app that works well

> **A good rule of thumb**
> If customers will only visit a few times a year, they will not download an app. Start with a website or web app, and add a mobile app once you have regular users.

## How the costs and timelines compare

A business website is the quickest and most affordable option, usually live in 2 to 3 weeks. A web app takes longer because it needs accounts, a database and more testing; a booking website typically takes 4 to 5 weeks. A mobile app is usually the biggest investment, as it must be built and tested for both iPhone and Android and approved by the app stores.

The good news is that one well-built back end can power all three. If you start with a website or web app built on solid foundations such as Laravel or Node.js, adding a mobile app later is much easier and cheaper.

## My recommendation

Start with the simplest thing that solves your problem today, and build it so it can grow. For most businesses that means a fast website first, a web app when admin starts to slow you down, and a mobile app once customers use your service regularly.

Not sure which fits you? [Book a free call](/#contact) and I will give you an honest recommendation, even if it is the smaller option.
MD,
                'faq' => [
                    ['question' => 'Is a web app the same as a website?', 'answer' => 'Not quite. Both run in a browser, but a website mainly shares information, while a web app lets people log in and do things such as book, order or manage data.'],
                    ['question' => 'Can a web app work on phones?', 'answer' => 'Yes. A well-built web app works on phones, tablets and computers without being installed, which makes it a good first step before a mobile app.'],
                    ['question' => 'Can I add a mobile app later?', 'answer' => 'Yes. If your website or web app has a well-built back end, a mobile app can use the same data and accounts, so adding it later is much simpler.'],
                    ['question' => 'Which option is best for getting found on Google?', 'answer' => 'A website. Search engines read website pages easily, while content inside a mobile app is not shown in Google search results.'],
                ],
            ],
            [
                'slug' => 'small-business-website-cost',
                'title' => 'How much does a small business website cost in 2026?',
                'seo_title' => 'How Much Does a Small Business Website Cost in 2026?',
                'description' => 'A clear breakdown of what a small business website costs in 2026, what affects the price, the hidden costs to watch for and how to get a fixed quote.',
                'category' => 'Pricing',
                'tags' => ['Website cost', 'Small business', 'Pricing', 'Websites'],
                'cover_alt' => 'Illustration of a business website with a price tag reading from £695',
                'service' => 'small-business-website-design',
                'body' => <<<'MD'
Ask ten web developers what a website costs and you will get ten different answers. That is frustrating when you are trying to plan a budget. This guide explains **what you actually pay for**, what changes the price and how to avoid surprise costs.

## Typical price ranges

Prices vary widely depending on who builds your website and how. As a rough guide:

- **DIY website builders**: low monthly fees, but you do all the work and often outgrow them
- **Freelance developer**: a custom website at a fixed price, with one person who knows your project
- **Agency**: usually the most expensive, with a larger team and more layers of management

For reference, my [business website package](/services/small-business-website-design) starts from £695 at a fixed price, and a [website with online booking](/services/online-booking-websites) starts from £1,295.

## What affects the price

### Number of pages

A five-page website (home, services, about, gallery and contact) costs less than a 30-page website with a page for every service and location. More pages means more design, writing and testing.

### Features

Contact forms are simple. Online booking, payments, customer accounts and connections to other software take more time, because they involve a database and careful testing.

### Design

A design made for your brand costs more than a template, but it helps you stand out and is built around what your customers need to see first.

### Content

If you supply your own text and photos, the cost is lower. Professional copywriting and photography add to the budget but often improve results.

## Hidden costs to watch for

- Domain name renewal each year
- Hosting, security updates and backups
- Paid plugins or themes that need yearly licences
- Charges for every small change after launch
- Being locked in, so you cannot move your website elsewhere

> **Always ask who owns the website**
> Make sure that once you have paid, the website, its code and its content belong to you. With me, they always do.

## Ongoing costs after launch

A website needs looking after, just like your premises. Budget for hosting, updates and the odd change. My [website care plan](/services/website-care-plans) covers this from £49 a month, with no long contract.

## How to get an accurate quote

1. List the pages you need and the features that matter most
2. Collect a few websites you like, and say what you like about them
3. Decide whether you will supply the text and photos
4. Ask for one fixed price in writing before any work begins

A good developer will give you a clear plan and a fixed price, not an hourly estimate that keeps growing. [Book a free call](/#contact) and I will send you exactly that.
MD,
                'faq' => [
                    ['question' => 'Why do website prices vary so much?', 'answer' => 'Because the work varies. The number of pages, the features, the design and who builds it all change the time involved. A fixed quote based on your actual needs is the only reliable price.'],
                    ['question' => 'Is a cheap website builder good enough?', 'answer' => 'It can be for a very simple start. But builders can be slow, harder to rank on Google and difficult to move away from, so many businesses switch to a custom website as they grow.'],
                    ['question' => 'Are there monthly costs after the website is built?', 'answer' => 'Usually yes: a domain name, hosting and maintenance. A care plan bundles these into one simple monthly price so nothing is forgotten.'],
                    ['question' => 'How long does it take to build a small business website?', 'answer' => 'A typical business website takes 2 to 3 weeks from the agreed plan to launch. Websites with online booking usually take 4 to 5 weeks.'],
                ],
            ],
        ];
    }
}
