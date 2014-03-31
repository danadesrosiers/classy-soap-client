classy-soap-client
==================

A wrapper to PHP's SoapClient that allows interaction will a SOAP service via classes.

Working with SOAP services in PHP can be challenging.  A SOAP service takes one or more arguments which are often classes and returns a object.  In order to use the SoapClient, the input must be mangled into an ugly associative array and the result is a stdClass.  By extending the ClassySoapClient, users can interact with the SOAP service in the way it was intented to be used.  You just need to define the method with the proper arguments and types.  If the `@return` annotation is used, the return will be converted to the specified class.  Be sure to use the fully qualified calss name.  Use [] to indicate and array.  Of course, you also need to define any input and return classes you use.

```
class SampleClient extends ClassySoapClient
{
  /**
   * @param InputClassA $one
   * @param             $two
   * @return SampleMethodReturn
   */
  public function sampleMethod(InputClassA $one, $two)
  {
    return $this->executeSoapCall(__FUNCTION__, get_defined_vars());
  }
  
  /**
   * @param InputClassB $one
   * @return OneArgMethodReturn[]
   */
  public function oneArgMethod(InputClassB $one)
  {
    return $this->executeSoapCall(__FUNCTION__, $one);
  }
}
```
