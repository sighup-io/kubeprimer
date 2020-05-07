# Secrets

A Secret is an object that contains a small amount of sensitive data such as a password, a token, or a key.

Kubernetes automatically creates secrets which contain credentials for accessing the API and it automatically modifies your pods to use this type of secret.

Secrets are stored as binaries in etcd and automatically encoded/decoded to base64 when retrieved.

## How to create a secret

You can create a secret by CLI:

`kubectl create secret generic db-user-pass --from-file=./username.txt --from-file=./password.txt`

or by `yaml` file:

```yaml
apiVersion: v1
kind: Secret
metadata:
  name: mysecret
type: Opaque
data:
  username: YWRtaW4= # echo -n "admin" | base64
  password: MWYyZDFlMmU2N2Rm # echo -n "1f2d1e2e67df" | base64
```

## Getting back secrets

```
kubectl get secret mysecret -o yaml | grep username | cut -f 4 -d ' ' | base64 --decode && echo ""
```

## Using secrets

You can use secrets both as shared volumes and as Environment Variables

### Using secrets as environment variables

```yaml
apiVersion: v1
kind: Pod
metadata:
  name: secret-env-pod
spec:
  containers:
    - name: mycontainer
      image: redis
      env:
        - name: SECRET_USERNAME
          valueFrom:
            secretKeyRef:
              name: mysecret
              key: username
        - name: SECRET_PASSWORD
          valueFrom:
            secretKeyRef:
              name: mysecret
              key: password
  restartPolicy: Never
```

### Using secrets as mounted volumes

You should mount a volume of type `secret` to the target `path`, that's really all!
