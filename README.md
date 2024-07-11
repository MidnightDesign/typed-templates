# midnight/typed-templates

## Type system

| Type      | Syntax                         | Notes               |
|-----------|--------------------------------|---------------------|
| String    | `string`                       |                     |
| Struct    | `{ foo: string, bar: string }` |                     |
| List      | `[string]`                     |                     |
| Boolean   | `true` or `false`              | Not implemented yet |
| Optionals | `string?`                      | Not implemented yet |

- There are no integers or floats. They should be formatted and passed as strings.
- There are no maps. You can use lists of structs containing the key and value.
