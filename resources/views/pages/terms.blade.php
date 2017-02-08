@extends ('layouts.main')

@section('content')
    <h1 class="mb-4">Subscription terms</h1>
    <p>
        By using the website <a href="beautiesfromheaven.com">beautiesfromheaven.com</a> you state that you have
        read and accepted these terms (<a href="beautiesfromheaven.com">beautiesfromheaven.com</a> is owned by
        WPG TO BE WORLD PHOTOGRAPHY LTD, no: HE39800)
    </p>
    <ul>
        <li>
            We provide a subscription service with three different subscription alternatives that will be automatically
            extended. 2 weeks subscription will charge you the sum every 2 weeks. 1 month subscription will charge you
            every month. 6 month subscription will charge you every 6 month. We will will charge you during this the
            choosen period of subscription until we receive termination notification of the service.
        </li>
        <li>
            To buy subscriptions you have to be over 18 years of age. This site contains adult content.
        </li>
        <li>
            To cancel subscriptions you contact us
            on <a href="mailto:support@beautiesfromheaven.com">support@beautiesfromheaven.com</a> with your
            Subscription ID.
        </li>
        <li>
            This subscription-service can at any time be canceled without prior notice. On cancelation the
            subscription will end when the subscription term has ended.
        </li>
        <li>
            The subscription password should be kept secret and is not going to be shared by third-party.
        </li>
        <li>
            Beautiesfromheaven do not apply the right to withdraw purchases unless there is an agreed upon reason or
            malfunction.
        </li>
        <li>
            Beautiesfromheaven is not responsible for future updates after you started the subscription service.
            password or any mis-usage should be reported to
            <a href="mailto:support@beautiesfromheaven.com">support@beautiesfromheaven.com</a> immediately. A new
            password will then be issued to the user. Banning of users that share password can occur.
        </li>
        <li>
            Beautiesfromheaven has the right to send out commercial messages via e-mail, sms or other distribution
            channel.
        </li>
        <li>
            The product is considered taken in to use after first time login with the subscription.
        </li>
        <li>
            As a customer buying subscription service at <a href="beautiesfromheaven.com">beautiesfromheaven.com</a> it
            is not allowed to give out personal information in comments on blogposts.
        </li>
    </ul>

    @if (url()->previous() !== url()->current())
        <a href="{{ url()->previous() }}" class="btn btn-primary">
            &laquo; Back
        </a>
    @endif
@endsection
