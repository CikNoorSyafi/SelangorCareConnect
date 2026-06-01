<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index()
    {
        // Volunteer dummy data
        $volunteers = [
            [
                'name' => 'Nurul Hidayah',
                'id' => 'V-1092'
            ],
            [
                'name' => 'Jason Khoo',
                'id' => 'V-2201'
            ],
            [
                'name' => 'Aminah Aziz',
                'id' => 'V-3321'
            ]
        ];

        // Campaign dummy storage
        $campaigns = session('campaigns', []);

        return view(
            'organizer.campaign',
            compact(
                'volunteers',
                'campaigns'
            )
        );
    }

    public function store(Request $request)
    {
        $campaigns = session('campaigns', []);

        $campaigns[] = [

            'id' => time(),

            'name' => $request->campaign_name,

            'type' => $request->campaign_type,

            'location' => $request->location,

            'target' => floatval(
                str_replace(',', '', $request->funding_target)
            ),

            'start_date' => $request->start_date,

            'end_date' => $request->end_date,

            'description' => $request->description,

            'status' => 'Approved',
            'volunteers' => $request->volunteers ?? [],

            'donors' => [],
        ];

        session([
            'campaigns' => $campaigns
        ]);

        return redirect('/campaign')
            ->with(
                'success',
                'Campaign created successfully.'
            );
    }

    public function delete($id)
    {
        $campaigns = session('campaigns', []);

        foreach ($campaigns as $key => $campaign) {
            if ($campaign['id'] == $id) {
                unset($campaigns[$key]);
            }
        }

        session([
            'campaigns' => array_values($campaigns)
        ]);

        return redirect('/campaign')
            ->with(
                'success',
                'Campaign deleted successfully.'
            );
    }

    public function edit($id)
    {
        $campaigns = session('campaigns', []);

        $campaign = null;

        foreach ($campaigns as $c) {
            if ($c['id'] == $id) {
                $campaign = $c;
                break;
            }
        }

        if (!$campaign) {
            return redirect('/campaign');
        }

        $volunteers = [
            [
                'name' => 'Nurul Hidayah',
                'id' => 'V-1092'
            ],
            [
                'name' => 'Jason Khoo',
                'id' => 'V-2201'
            ],
            [
                'name' => 'Aminah Aziz',
                'id' => 'V-3321'
            ]
        ];

        return view(
            'organizer.edit-campaign',
            compact(
                'campaign',
                'volunteers'
            )
        );
    }

    public function update(Request $request, $id)
    {
        $campaigns = session('campaigns', []);

        foreach ($campaigns as &$campaign) {
            if ($campaign['id'] == $id) {
                $campaign['name'] = $request->campaign_name;

                $campaign['location'] = $request->location;

                $campaign['target'] = (float) 
                    str_replace(
                        ',',
                        '',
                        $request->funding_target
                    );

                $campaign['description'] =
                    $request->description;
                $campaign['volunteers']
                    = $request->volunteers ?? [];
            }
        }

        session([
            'campaigns' => $campaigns
        ]);

        return redirect('/campaign')
            ->with(
                'success',
                'Campaign updated successfully.'
            );
    }
}