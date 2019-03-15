<?php

namespace Larafun\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpFoundation\AcceptHeaderItem;

/**
 * Laravel $request->wantsJson() checks to see if the first item
 * of the Accept Header has a /json defined media type.
 * 
 * Your API consumers might omit this header and this could lead
 * to unwanted API behaviours (eg: redirecting on ValidationExceptions)
 * 
 * This middleware will help by adding the application/json in the
 * Request Accept Header, without overwriting any existing headers.
 * An optional quality parameter may be used to control the priority.
 * In case of equal quality parameters, the application/json will
 * have precedence, due to a lower index.
 * 
 * You should also check your HTTP server configuration as it might
 * add a default Header with quality 1. In this case, you should
 * always use the default quality on this Middleware for the
 * feature to take effect.
 */
class AcceptJsonMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $quality = 1, $force = null)
    {
        $header = AcceptHeader::fromString($request->headers->get('accept'));

        /**
         * Check wether the new header should be added or not.
         */
        if ($header->has('application/json') && (trim($force) !== 'force')) {
            return $next($request);
        }

        /**
         * We set the index to -1 so that it will have priority over
         * other accept headers with the same quality
         */
        $header->add(
            (new AcceptHeaderItem('application/json', [
                'q' => $quality,
            ]))->setIndex(-1)
        );

        $request->headers->set('accept', (string) $header);

        return $next($request);
    }
}
