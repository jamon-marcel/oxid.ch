<?php
namespace App\Http\Controllers\Api;
use App\Models\Contact;
use App\Http\Resources\DataCollection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactController extends Controller
{
  protected $contact;
  
  /**
   * Constructor
   * 
   * @param Contact $contact
   */

  public function __construct(Contact $contact)
  {
    $this->contact = $contact;
  }

  /**
   * Get all records
   *
   * @return \Illuminate\Http\Response
   */

  public function get()
  {
    return response()->json($this->contact->get());
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  
  public function store(Request $request)
  {   
    $contact = new Contact([
      'address' => [
        'de' => $request->input('address.de'),
        'en' => $request->input('address.en'),
      ],
      'google_maps_url' => $request->input('google_maps_url'),
      'contacts' => [
        'de' => $request->input('contacts.de'),
        'en' => $request->input('contacts.en'),
      ],
      'info' => [
        'de' => $request->input('info.de'),
        'en' => $request->input('info.en'),
      ],
      'imprint' => [
        'de' => $request->input('imprint.de'),
        'en' => $request->input('imprint.en'),
      ],
    ]);
    $contact->save();
    return response()->json(['contactId' => $contact->id]);
  }

  /**
   * Edit a specified resource.
   *
   * @param Contact $contact
   * @return \Illuminate\Http\Response
   */

  public function edit(Contact $contact)
  {
    $contact = $this->contact->findOrFail($contact->id);
    return response()->json($contact);
  }

  /**
   * Update the status of the specified resource.
   *
   * @param Contact $contact
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */

  public function update(Contact $contact, Request $request)
  {
    $contact = $this->contact->findOrFail($contact->id);
    $contact->setTranslation('address', 'de', $request->input('address.de'));
    $contact->setTranslation('address', 'en', $request->input('address.en'));
    $contact->google_maps_url = $request->input('google_maps_url');
    $contact->setTranslation('contacts', 'de', $request->input('contacts.de'));
    $contact->setTranslation('contacts', 'en', $request->input('contacts.en'));
    $contact->setTranslation('info', 'de', $request->input('info.de'));
    $contact->setTranslation('info', 'en', $request->input('info.en'));
    $contact->setTranslation('imprint', 'de', $request->input('imprint.de'));
    $contact->setTranslation('imprint', 'en', $request->input('imprint.en'));
    $contact->save();
    return response()->json('successfully updated');
  }

}
