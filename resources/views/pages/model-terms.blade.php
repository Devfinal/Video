@extends ('layouts.main')

@section('content')
    <h1 class="mb-4">Terms of use</h1>
    <p>
        Upon being accepted as a picture/video blogger at <a href="{{ route('home') }}">beautiesfromheaven.com</a> i
        hereby give my acceptance to WPG TO BE WORLD PHOTOGRAPHY LTD Org nr: HE39800 the right to publish my pictures
        and videos on the <a href="{{ route('home') }}">beautiesfromheaven.com</a> and for publishing on social media
        and for marketing purposes. The copyright to the pictures/videos are given to the company WPG TO BE WORLD
        PHOTOGRAPHY LTD. I´m fully aware that the pictures/videos are published as a subscription service and therefore
        certain rules apply. I can only delete my blogposts pictures/videos 5 days back in time. Upon deactivation of my
        profile I agree that the pictures/videos and profile will be active for two more months before it is
        unpublished, that gives customers a chance to unsubscribe if they wish so. I will get access to a webshop where
        I can buy products and change my earned hearts into cash. Hearts given by customers on pictures/videos published
        functions as a currency in our webshop and will be compensation for publishing pictures/videos at
        <a href="{{ route('home') }}">beautiesfromheaven.com</a>. There will be a onetime fee for each new subscriber
        signing up for the website from my profile.
    </p>

    @if (url()->previous() !== url()->current())
        <a href="{{ url()->previous() }}" class="btn btn-primary">
            &laquo; Back
        </a>
    @endif
@endsection
